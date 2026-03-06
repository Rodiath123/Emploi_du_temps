<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
        /* Effet de verre version Claire */
        .glass-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .grid-line {
            height: 80px;
        }
    </style>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <main class="w-full flex flex-col bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-200">
                
                <header class="h-24 flex items-center justify-between px-10 border-b border-gray-100 bg-white/80 backdrop-blur-md">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Planning de la Semaine</h1>
                        <p class="text-xs text-gray-500 font-medium italic mt-0.5">ESGIS - Institut d'Excellence</p>
                    </div>
                    @if(auth()->user()->role === 'admin')
                        <button onclick="openAddModal()"
                            class="bg-indigo-600 text-white hover:bg-indigo-700 px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg transition-all active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-plus text-[10px]"></i> Nouveau Cours
                        </button>
                    @endif
                </header>

                <div class="flex-1 overflow-y-auto p-10 bg-gray-50/50">
                    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-xl relative overflow-hidden">
                        
                        <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/50">
                            <div class="p-6 text-center border-r border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Heures</div>
                            @foreach($days as $day)
                                <div class="p-6 text-center border-r border-gray-100 last:border-0 text-[11px] font-black text-gray-700 uppercase tracking-[0.2em]">
                                    {{ $day }}
                                </div>
                            @endforeach
                        </div>

                        <div class="flex relative">
                            <div class="w-[14.28%] border-r border-gray-100">
                                @foreach($hours as $hour)
                                    <div class="grid-line border-b border-gray-100 flex items-center justify-center relative">
                                        <span class="text-[10px] font-black text-gray-400 bg-white px-2">{{ sprintf('%02d:00', $hour) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex-1 grid grid-cols-6 relative h-full">
                                @foreach($days as $day)
                                    <div class="relative border-r border-gray-100 last:border-0 h-full">
                                        @foreach($courses->where('day', $day) as $course)
                                            @php
                                                // ... (garder ton code @php identique pour le calcul des positions) ...
                                                $startHour = intval(substr($course->start_time, 0, 2));
                                                $startMin = intval(substr($course->start_time, 3, 2));
                                                $endHour = intval(substr($course->end_time, 0, 2));
                                                $endMin = intval(substr($course->end_time, 3, 2));
                                                $top = (($startHour - 7) * 80) + ($startMin * 80 / 60);
                                                $duration = (($endHour * 60) + $endMin) - (($startHour * 60) + $startMin);
                                                $height = ($duration * 80 / 60);

                                                $themes = [
                                                    'bg-blue-50 border-blue-200 text-blue-700',
                                                    'bg-purple-50 border-purple-200 text-purple-700',
                                                    'bg-emerald-50 border-emerald-200 text-emerald-700',
                                                    'bg-amber-50 border-amber-200 text-amber-700',
                                                    'bg-rose-50 border-rose-200 text-rose-700'
                                                ];
                                                $theme = $themes[abs(crc32($course->subject)) % count($themes)];
                                            @endphp

                                            <div class="absolute inset-x-2 rounded-2xl p-4 border shadow-sm group cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] hover:z-30 {{ $theme }}"
                                                style="top: {{ $top }}px; height: {{ $height }}px;"
                                                onclick="editCourse({{ json_encode($course) }})">
                                                <div class="flex flex-col h-full">
                                                    <span class="text-[9px] font-black uppercase opacity-70">{{ $course->class_name }}</span>
                                                    <p class="text-[10px] font-black leading-tight break-words uppercase mt-1 px-1">
                                                    {{ $course->subject }}</p>
                                                    <div class="mt-auto flex items-center justify-between border-t border-current/10 pt-2">
                                                        <span class="text-[10px] font-bold">{{ $course->room }}</span>
                                                        <span class="text-[9px] opacity-60 italic">{{ substr($course->start_time, 0, 5) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="modalCours" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-6">
    <div class="bg-white border border-gray-200 w-full max-w-lg p-10 rounded-[2.5rem] shadow-2xl">
        <div class="flex justify-between items-center mb-10">
            <h3 id="modalTitle" class="text-xl font-black text-gray-900 uppercase">Édition du cours</h3>
            <button type="button" onclick="document.getElementById('modalCours').classList.add('hidden')"
                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="courseForm" action="{{ route('courses.store') }}" method="POST" class="space-y-6">
            @csrf
            <div id="methodField"></div>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Matière</label>
                    <input type="text" name="subject" id="subject" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Jour</label>
                    <select name="day" id="day" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 font-bold">
                        @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Classe</label>
                    <select name="class_name" id="class_name" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 font-bold">
                        <option value="">-- Sélectionner --</option>
                        @isset($classes)
                            @foreach($classes as $class)
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Début</label>
                    <input type="time" name="start_time" id="start_time" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 font-bold">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Fin</label>
                    <input type="time" name="end_time" id="end_time" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 font-bold">
                </div>

                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Salle</label>
                    <select name="room" id="room" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-gray-900 font-bold">
                        <option value="">-- Sélectionner --</option>
                        @isset($rooms)
                            @foreach($rooms as $room)
                                <option value="{{ $room->name }}">{{ $room->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-indigo-600 text-white font-black py-5 rounded-2xl uppercase text-[11px] tracking-widest hover:bg-indigo-700 transition-all shadow-lg">
                    Enregistrer
                </button>
                <button type="button" id="deleteBtn" class="hidden bg-red-50 text-red-600 p-5 rounded-2xl hover:bg-red-600 hover:text-white transition-all">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </form>

        <form id="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
            
            </div>
    </div>
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Nouveau Cours";
            document.getElementById('courseForm').action = "{{ route('courses.store') }}";
            document.getElementById('methodField').innerHTML = "";
            document.getElementById('courseForm').reset();
            document.getElementById('deleteBtn').classList.add('hidden');
            document.getElementById('modalCours').classList.remove('hidden');
        }

        function editCourse(course) {
            document.getElementById('modalTitle').innerText = "Modifier le cours";
            document.getElementById('courseForm').action = "/courses/" + course.id;
            document.getElementById('methodField').innerHTML = '@method("PUT")';

            document.getElementById('subject').value = course.subject;
            document.getElementById('day').value = course.day;
            document.getElementById('class_name').value = course.class_name;
            document.getElementById('start_time').value = course.start_time.substring(0, 5);
            document.getElementById('end_time').value = course.end_time.substring(0, 5);
            document.getElementById('room').value = course.room;

            const deleteBtn = document.getElementById('deleteBtn');
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = "/courses/" + course.id;
            
            deleteBtn.classList.remove('hidden');
            deleteBtn.onclick = () => {
                if (confirm('Voulez-vous supprimer ce cours ?')) deleteForm.submit();
            };

            document.getElementById('modalCours').classList.remove('hidden');
        }
    </script>
</x-app-layout>