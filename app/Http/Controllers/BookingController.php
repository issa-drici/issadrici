<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function show($token)
    {
        $bookingLink = BookingLink::where('token', $token)->firstOrFail();
        
        if (!$bookingLink->isValid()) {
            return view('booking.expired', ['bookingLink' => $bookingLink]);
        }
        
        $form = $bookingLink->bookingForm;
        
        // Générer les créneaux disponibles pour les 30 prochains jours
        $creneauxDisponibles = [];
        $creneauxConfig = $form->creneaux_disponibles ?? [];
        $joursMapping = [
            'lundi' => 1,
            'mardi' => 2,
            'mercredi' => 3,
            'jeudi' => 4,
            'vendredi' => 5,
            'samedi' => 6,
            'dimanche' => 0,
        ];
        
        $dateDebut = now();
        $dateFin = now()->addDays(30);
        
        for ($date = $dateDebut->copy(); $date->lte($dateFin); $date->addDay()) {
            $jourSemaine = $date->dayOfWeek;
            
            foreach ($creneauxConfig as $creneau) {
                $jourCreneau = $joursMapping[strtolower($creneau['jour'] ?? '')] ?? null;
                
                if ($jourCreneau === $jourSemaine) {
                    $heureDebut = \Carbon\Carbon::parse($creneau['heure_debut']);
                    $heureFin = \Carbon\Carbon::parse($creneau['heure_fin']);
                    
                    // Générer des créneaux toutes les 30 minutes
                    $heureActuelle = $heureDebut->copy();
                    while ($heureActuelle->lt($heureFin)) {
                        $creneauDateTime = $date->copy()->setTime($heureActuelle->hour, $heureActuelle->minute);
                        
                        // Ne proposer que les créneaux futurs
                        if ($creneauDateTime->isFuture()) {
                            // Vérifier qu'il n'y a pas déjà une réservation
                            $existingBooking = Booking::where('date_choisie', $creneauDateTime)
                                ->where('statut', '!=', 'annule')
                                ->first();
                            
                            if (!$existingBooking) {
                                $creneauxDisponibles[] = [
                                    'datetime' => $creneauDateTime->format('Y-m-d\TH:i'),
                                    'date' => $creneauDateTime->format('Y-m-d'),
                                    'time' => $creneauDateTime->format('H:i'),
                                    'label' => $creneauDateTime->format('d/m/Y à H:i'),
                                ];
                            }
                        }
                        
                        $heureActuelle->addMinutes(30);
                    }
                }
            }
        }
        
        // Trier par date
        usort($creneauxDisponibles, fn($a, $b) => strcmp($a['datetime'], $b['datetime']));
        
        return view('booking.show', [
            'bookingLink' => $bookingLink,
            'form' => $form,
            'creneauxDisponibles' => $creneauxDisponibles,
        ]);
    }
    
    public function store(Request $request, $token)
    {
        $bookingLink = BookingLink::where('token', $token)->firstOrFail();
        
        if (!$bookingLink->isValid()) {
            return back()->withErrors(['error' => 'Ce lien de réservation n\'est plus valide.']);
        }
        
        $request->validate([
            'date_choisie' => 'required|date|after:now',
            'email' => 'required|email',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
        ]);
        
        // Vérifier que le créneau est disponible
        $dateChoisie = \Carbon\Carbon::parse($request->date_choisie);
        $form = $bookingLink->bookingForm;
        
        // Vérifier les créneaux disponibles
        $creneauxDisponibles = $form->creneaux_disponibles ?? [];
        $jourSemaine = $dateChoisie->dayOfWeek; // 0 = dimanche, 1 = lundi, etc.
        $heure = $dateChoisie->format('H:i');
        
        // Mapping des jours français vers les jours de la semaine (0-6)
        $joursMapping = [
            'dimanche' => 0,
            'lundi' => 1,
            'mardi' => 2,
            'mercredi' => 3,
            'jeudi' => 4,
            'vendredi' => 5,
            'samedi' => 6,
        ];
        
        $creneauValide = false;
        if (!empty($creneauxDisponibles)) {
            foreach ($creneauxDisponibles as $creneau) {
                $jourCreneau = strtolower($creneau['jour'] ?? '');
                $jourCreneauNum = $joursMapping[$jourCreneau] ?? null;
                
                if ($jourCreneauNum === $jourSemaine) {
                    $heureDebut = \Carbon\Carbon::parse($creneau['heure_debut'])->format('H:i');
                    $heureFin = \Carbon\Carbon::parse($creneau['heure_fin'])->format('H:i');
                    if ($heure >= $heureDebut && $heure < $heureFin) {
                        $creneauValide = true;
                        break;
                    }
                }
            }
        } else {
            // Si pas de créneaux définis, on accepte toutes les heures
            $creneauValide = true;
        }
        
        if (!$creneauValide) {
            return back()->withErrors(['date_choisie' => 'Ce créneau n\'est pas disponible.']);
        }
        
        // Vérifier qu'il n'y a pas déjà une réservation à ce créneau
        $existingBooking = Booking::where('date_choisie', $dateChoisie)
            ->where('statut', '!=', 'annule')
            ->first();
        
        if ($existingBooking) {
            return back()->withErrors(['date_choisie' => 'Ce créneau est déjà réservé.']);
        }
        
        // Normaliser les données pour la recherche (tolower, trim, suppression accents optionnelle)
        $normalize = function($str) {
            if (empty($str)) return '';
            $str = mb_strtolower(trim($str));
            // Supprimer les accents pour une recherche plus permissive
            $str = str_replace(
                ['à', 'á', 'â', 'ã', 'ä', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ç'],
                ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'c'],
                $str
            );
            return $str;
        };
        
        $emailNormalized = $normalize($request->email);
        $prenomNormalized = $normalize($request->prenom);
        $nomNormalized = $normalize($request->nom);
        $societeNormalized = $normalize($request->societe ?? 'Non renseigné');
        
        // Chercher un prospect existant pour éviter les doublons
        // 1. D'abord par email (le plus fiable) - recherche exacte mais normalisée
        if ($emailNormalized) {
            $prospect = \App\Models\Prospect::whereRaw('LOWER(TRIM(email)) = ?', [$emailNormalized])->first();
        }
        
        // 2. Si pas trouvé par email, chercher par nom + prénom + société (normalisés)
        if (!$prospect) {
            $prospect = \App\Models\Prospect::whereRaw('LOWER(TRIM(prenom)) = ?', [$prenomNormalized])
                ->whereRaw('LOWER(TRIM(nom)) = ?', [$nomNormalized])
                ->whereRaw('LOWER(TRIM(societe)) = ?', [$societeNormalized])
                ->first();
        }
        
        // 3. Si toujours pas trouvé, chercher par nom + prénom seulement (normalisés)
        // Mais seulement si les noms sont assez longs (éviter les faux positifs avec "Jean" ou "Marie")
        if (!$prospect && strlen($prenomNormalized) >= 3 && strlen($nomNormalized) >= 3) {
            $prospect = \App\Models\Prospect::whereRaw('LOWER(TRIM(prenom)) = ?', [$prenomNormalized])
                ->whereRaw('LOWER(TRIM(nom)) = ?', [$nomNormalized])
                ->first();
        }
        
        // 4. Recherche par similarité de nom (distance de Levenshtein) pour gérer les fautes de frappe
        // Mais seulement si on a un email ET que les noms sont assez longs
        if (!$prospect && $emailNormalized && strlen($nomNormalized) >= 4) {
            $prospects = \App\Models\Prospect::whereNotNull('email')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$emailNormalized])
                ->get();
            
            // Si l'email correspond mais pas le nom exact, vérifier la similarité
            foreach ($prospects as $p) {
                $pNomNormalized = $normalize($p->nom);
                $pPrenomNormalized = $normalize($p->prenom);
                
                // Distance de Levenshtein pour le nom (tolérance de 1-2 caractères)
                $distanceNom = levenshtein($nomNormalized, $pNomNormalized);
                $distancePrenom = levenshtein($prenomNormalized, $pPrenomNormalized);
                
                // Si la distance est faible (1-2 caractères max) et les noms sont similaires
                if ($distanceNom <= 2 && $distancePrenom <= 2 && 
                    ($distanceNom + $distancePrenom) <= 3) {
                    $prospect = $p;
                    break;
                }
            }
        }
        
        // Si pas de prospect trouvé, créer un nouveau
        if (!$prospect) {
            $prospect = \App\Models\Prospect::create([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'societe' => $request->societe ?? 'Non renseigné',
                'statut' => 'call_planifie',
                'canal_principal' => 'email',
            ]);
        } else {
            // Mettre à jour les infos si nécessaire (surtout l'email si manquant)
            $updateData = [];
            
            // Mettre à jour l'email si le prospect n'en avait pas
            if (empty($prospect->email) && $request->email) {
                $updateData['email'] = $request->email;
            }
            
            // Mettre à jour le prénom/nom si différents (normaliser)
            if ($prospect->prenom !== $request->prenom) {
                $updateData['prenom'] = $request->prenom;
            }
            if ($prospect->nom !== $request->nom) {
                $updateData['nom'] = $request->nom;
            }
            
            // Mettre à jour la société si elle était "Non renseigné" et qu'on a maintenant une vraie société
            if (($prospect->societe === 'Non renseigné' || empty($prospect->societe)) && $request->societe) {
                $updateData['societe'] = $request->societe;
            }
            
            // Mettre à jour le statut si nécessaire
            if ($prospect->statut === 'a_contacter' || $prospect->statut === 'contacte') {
                $updateData['statut'] = 'call_planifie';
            }
            
            if (!empty($updateData)) {
                $prospect->update($updateData);
            }
        }
        
        // Créer ou récupérer l'utilisateur
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => "{$request->prenom} {$request->nom}",
                'email' => $request->email,
                'password' => Hash::make(\Illuminate\Support\Str::random(32)), // Mot de passe aléatoire, sera réinitialisé plus tard
            ]);
        }
        
        // Collecter les données du formulaire personnalisé
        $donneesFormulaire = [];
        $champsForm = $form->champs ?? [];
        foreach ($champsForm as $champ) {
            $key = \Illuminate\Support\Str::slug($champ['label']);
            if ($request->has($key)) {
                $donneesFormulaire[$champ['label']] = $request->input($key);
            }
        }
        
        // Ajouter les données de base
        $donneesFormulaire['prenom'] = $request->prenom;
        $donneesFormulaire['nom'] = $request->nom;
        $donneesFormulaire['email'] = $request->email;
        if ($request->societe) {
            $donneesFormulaire['societe'] = $request->societe;
        }
        
        // Créer la réservation
        $booking = Booking::create([
            'booking_link_id' => $bookingLink->id,
            'prospect_id' => $prospect->id,
            'user_id' => $user->id,
            'date_choisie' => $dateChoisie,
            'donnees_formulaire' => $donneesFormulaire,
            'statut' => 'confirme',
        ]);
        
        // Créer un call associé au prospect et à la réservation
        $prospect->calls()->create([
            'booking_id' => $booking->id,
            'date_planifiee' => $dateChoisie,
            'objectif_call' => 'Call réservé via formulaire de réservation',
            'statut' => 'planifie',
        ]);
        
        return view('booking.success', [
            'booking' => $booking,
            'bookingLink' => $bookingLink,
        ]);
    }
}
