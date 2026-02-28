<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un call</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .booking-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            width: 100%;
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        .error {
            color: #ef4444;
            font-size: 14px;
            margin-top: 4px;
        }
        .calendar-container {
            margin: 30px 0;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-nav-btn {
            background: #f3f4f6;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.2s;
        }
        .calendar-nav-btn:hover {
            background: #e5e7eb;
        }
        .calendar-month {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }
        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #6b7280;
            padding: 8px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .calendar-day:hover {
            border-color: #667eea;
            background: #f3f4f6;
        }
        .calendar-day.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .calendar-day.disabled {
            color: #d1d5db;
            cursor: not-allowed;
            background: #f9fafb;
        }
        .calendar-day.today {
            border-color: #667eea;
            font-weight: 700;
        }
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .time-slot {
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            font-weight: 500;
        }
        .time-slot:hover {
            border-color: #667eea;
            background: #f3f4f6;
        }
        .time-slot.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .time-slot.disabled {
            color: #d1d5db;
            cursor: not-allowed;
            background: #f9fafb;
        }
        .selected-date-info {
            background: #f3f4f6;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h1 style="font-size: 28px; margin-bottom: 8px; color: #1f2937;">Réserver un call</h1>
        @if($bookingLink->description)
            <p style="color: #6b7280; margin-bottom: 30px;">{{ $bookingLink->description }}</p>
        @endif
        
        @if($errors->any())
            <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('booking.store', $bookingLink->token) }}" id="bookingForm">
            @csrf
            
            <input type="hidden" id="date_choisie" name="date_choisie" value="{{ old('date_choisie') }}" required>
            
            @if(!empty($creneauxDisponibles))
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button type="button" class="calendar-nav-btn" onclick="changeMonth(-1)">‹</button>
                        <div class="calendar-month" id="currentMonth"></div>
                        <button type="button" class="calendar-nav-btn" onclick="changeMonth(1)">›</button>
                    </div>
                    
                    <div class="calendar-grid" id="calendarGrid"></div>
                    
                    <div id="timeSlotsContainer" class="hidden">
                        <h3 style="font-size: 18px; margin-bottom: 12px; color: #1f2937;">Sélectionnez un créneau horaire</h3>
                        <div class="time-slots" id="timeSlots"></div>
                    </div>
                    
                    <div id="selectedDateInfo" class="selected-date-info hidden">
                        <p style="font-weight: 600; margin-bottom: 4px;">Créneau sélectionné :</p>
                        <p id="selectedDateTime"></p>
                    </div>
                </div>
            @else
                <div class="form-group">
                    <label for="date_choisie_input">Date et heure du call *</label>
                    <input type="datetime-local" id="date_choisie_input" name="date_choisie" value="{{ old('date_choisie') }}" required min="{{ now()->format('Y-m-d\TH:i') }}">
                    <small style="color: #f59e0b; display: block; margin-top: 4px;">⚠️ Aucun créneau configuré dans le formulaire. Vous pouvez choisir n'importe quelle date/heure.</small>
                </div>
            @endif
            
            <div style="border-top: 2px solid #e5e7eb; margin: 30px 0; padding-top: 30px;">
                <h2 style="font-size: 20px; margin-bottom: 20px; color: #1f2937;">Vos informations</h2>
                
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                    @error('prenom')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required>
                    @error('nom')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="societe">Société</label>
                    <input type="text" id="societe" name="societe" value="{{ old('societe') }}">
                </div>
                
                @php
                    $champs = $form->champs ?? [];
                @endphp
                
                @foreach($champs as $champ)
                    <div class="form-group">
                        <label for="{{ \Illuminate\Support\Str::slug($champ['label']) }}">{{ $champ['label'] }}@if($champ['required'] ?? false) * @endif</label>
                        @if(($champ['type'] ?? 'text') === 'textarea')
                            <textarea id="{{ \Illuminate\Support\Str::slug($champ['label']) }}" name="{{ \Illuminate\Support\Str::slug($champ['label']) }}" rows="4" @if($champ['required'] ?? false) required @endif>{{ old(\Illuminate\Support\Str::slug($champ['label'])) }}</textarea>
                        @elseif(($champ['type'] ?? 'text') === 'select')
                            <select id="{{ \Illuminate\Support\Str::slug($champ['label']) }}" name="{{ \Illuminate\Support\Str::slug($champ['label']) }}" @if($champ['required'] ?? false) required @endif>
                                <option value="">Sélectionnez...</option>
                                @foreach(explode("\n", $champ['options'] ?? '') as $option)
                                    @if(trim($option))
                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $champ['type'] ?? 'text' }}" id="{{ \Illuminate\Support\Str::slug($champ['label']) }}" name="{{ \Illuminate\Support\Str::slug($champ['label']) }}" value="{{ old(\Illuminate\Support\Str::slug($champ['label'])) }}" @if($champ['required'] ?? false) required @endif>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <small style="color: #6b7280; display: block; margin-bottom: 20px;">Durée du call : {{ $form->duree_call }} minutes</small>
            
            <button type="submit" class="btn-primary" id="submitBtn" disabled>Réserver ce créneau</button>
        </form>
    </div>
    
    @if(!empty($creneauxDisponibles))
    <script>
        const creneaux = @json($creneauxDisponibles);
        let currentDate = new Date();
        let selectedDate = null;
        let selectedTime = null;
        
        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const dayNames = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        
        // Organiser les créneaux par date
        const creneauxByDate = {};
        creneaux.forEach(creneau => {
            const dateKey = creneau.date;
            if (!creneauxByDate[dateKey]) {
                creneauxByDate[dateKey] = [];
            }
            creneauxByDate[dateKey].push(creneau);
        });
        
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            renderCalendar();
        }
        
        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            
            // Headers
            dayNames.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                grid.appendChild(header);
            });
            
            // Empty cells for days before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                const empty = document.createElement('div');
                grid.appendChild(empty);
            }
            
            // Days of the month
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateKey = date.toISOString().split('T')[0];
                const hasCreneaux = creneauxByDate[dateKey] && creneauxByDate[dateKey].length > 0;
                const isPast = date < today;
                const isSelected = selectedDate && dateKey === selectedDate;
                
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                if (isPast || !hasCreneaux) {
                    dayElement.classList.add('disabled');
                } else {
                    dayElement.onclick = () => selectDate(dateKey);
                }
                
                if (date.toDateString() === today.toDateString()) {
                    dayElement.classList.add('today');
                }
                
                if (isSelected) {
                    dayElement.classList.add('selected');
                }
                
                grid.appendChild(dayElement);
            }
        }
        
        function selectDate(dateKey) {
            selectedDate = dateKey;
            selectedTime = null;
            renderCalendar();
            renderTimeSlots(dateKey);
            document.getElementById('timeSlotsContainer').classList.remove('hidden');
            document.getElementById('selectedDateInfo').classList.add('hidden');
            document.getElementById('submitBtn').disabled = true;
        }
        
        function renderTimeSlots(dateKey) {
            const slots = creneauxByDate[dateKey] || [];
            const container = document.getElementById('timeSlots');
            container.innerHTML = '';
            
            if (slots.length === 0) {
                container.innerHTML = '<p style="color: #6b7280; text-align: center; grid-column: 1 / -1;">Aucun créneau disponible ce jour</p>';
                return;
            }
            
            slots.forEach(slot => {
                const timeStr = slot.time;
                const slotElement = document.createElement('div');
                slotElement.className = 'time-slot';
                slotElement.textContent = timeStr;
                slotElement.onclick = () => selectTime(slot.datetime, timeStr);
                
                if (selectedTime === slot.datetime) {
                    slotElement.classList.add('selected');
                }
                
                container.appendChild(slotElement);
            });
        }
        
        function selectTime(datetime, timeStr) {
            selectedTime = datetime;
            // Convertir en format datetime-local pour le formulaire
            const date = new Date(datetime);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            document.getElementById('date_choisie').value = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            // Update UI
            document.querySelectorAll('.time-slot').forEach(el => {
                el.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            const dateStr = date.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('selectedDateTime').textContent = `${dateStr} à ${timeStr}`;
            document.getElementById('selectedDateInfo').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = false;
        }
        
        // Initialize
        renderCalendar();
    </script>
    @endif
</body>
</html>
