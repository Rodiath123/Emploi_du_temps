<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESGIS - Planning Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
        }

        .grid-line {
            height: 80px;
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-200 font-sans antialiased overflow-hidden">

    <div class="flex h-screen">
        <aside class="w-72 bg-[#0f172a] border-r border-slate-800/50 flex flex-col relative z-20">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-10">
                    <div class="bg-white p-1.5 rounded-xl shadow-2xl w-10 h-10 flex-shrink-0">
                        <img src="{{ asset('logo-esgis.png') }}" alt="L" class="w-full h-full object-contain">
                    </div>
                    <span class="text-xl font-black tracking-tighter text-white">ESGIS <span
                            class="text-indigo-500">PRO</span></span>
                </div>

                <nav class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Menu Principal
                        </p>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 bg-indigo-500/10 text-indigo-400 rounded-2xl border border-indigo-500/20 shadow-lg shadow-indigo-500/5">
                            <i class="fa-solid fa-grid-2 w-5 text-sm"></i>
                            <span class="font-bold text-sm">Emploi du Temps</span>
                        </a>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Filtres</p>
                        <div class="relative group">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                            <input type="text" placeholder="Rechercher..."
                                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-xs focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>
                </nav>
            </div>

            <div class="mt-auto p-6">
                <div class="bg-slate-900/80 rounded-3xl p-5 border border-slate-800 shadow-xl">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-black text-white shadow-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white leading-none">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-indigo-400 mt-1 font-black uppercase tracking-widest">
                                {{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}"
                        class="mt-4 border-t border-slate-800 pt-4 text-center">
                        @csrf
                        <button type="submit"
                            class="text-[11px] text-red-400/70 hover:text-red-400 font-black uppercase tracking-widest transition-colors">Déconnexion</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 bg-[#020617] relative">
            <header
                class="h-24 flex items-center justify-between px-10 border-b border-slate-800/50 bg-[#020617]/80 backdrop-blur-md sticky top-0 z-10">
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Planning de la Semaine</h1>
                    <p class="text-xs text-slate-500 font-medium italic mt-0.5">ESGIS - Institut d'Excellence</p>
                </div>
                @if(auth()->user()->role === 'admin')
                    <button onclick="openAddModal()"
                        class="bg-white text-black hover:bg-indigo-50 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl transition-all active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-[10px]"></i> Nouveau Cours
                    </button>
                @endif
            </header>

            <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="glass rounded-[2rem] border border-white/5 shadow-2xl relative">
                    <div class="grid grid-cols-7 border-b border-white/5 bg-white/[0.02]">
                        <div
                            class="p-6 text-center border-r border-white/5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                            Heures</div>
                        @foreach($days as $day)
                            <div
                                class="p-6 text-center border-r border-white/5 last:border-0 text-[11px] font-black text-white uppercase tracking-[0.2em]">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <div class="flex relative">
                        <div class="w-[14.28%] border-r border-white/5">
                            @foreach($hours as $hour)
                                <div class="grid-line border-b border-white/5 flex items-center justify-center relative">
                                    <span
                                        class="text-[10px] font-black text-slate-600 bg-[#020617] px-2">{{ sprintf('%02d:00', $hour) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex-1 grid grid-cols-6 relative h-full">
                            @foreach($days as $day)
                                <div class="relative border-r border-white/5 last:border-0 h-full">
                                    @foreach($courses->where('day', $day) as $course)
                                        @php
                                            $startHour = intval(substr($course->start_time, 0, 2));
                                            $startMin = intval(substr($course->start_time, 3, 2));
                                            $endHour = intval(substr($course->end_time, 0, 2));
                                            $endMin = intval(substr($course->end_time, 3, 2));

                                            $top = (($startHour - 7) * 80) + ($startMin * 80 / 60);
                                            $duration = (($endHour * 60) + $endMin) - (($startHour * 60) + $startMin);
                                            $height = ($duration * 80 / 60);

                                            $themes = [
                                                'bg-blue-500/10 border-blue-500/30 text-blue-400',
                                                'bg-purple-500/10 border-purple-500/30 text-purple-400',
                                                'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                                                'bg-amber-500/10 border-amber-500/30 text-amber-400',
                                                'bg-rose-500/10 border-rose-500/30 text-rose-400'
                                            ];
                                            $theme = $themes[abs(crc32($course->subject)) % count($themes)];
                                        @endphp

                                        <div class="absolute inset-x-2 rounded-2xl p-4 border shadow-2xl group cursor-pointer transition-all hover:scale-[1.03] hover:z-30 {{ $theme }}"
                                            style="top: {{ $top }}px; height: {{ $height }}px;"
                                            onclick="editCourse({{ json_encode($course) }})">
                                            <div class="flex flex-col h-full">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span
                                                        class="text-[9px] font-black uppercase tracking-tighter opacity-60">{{ $course->class_name }}</span>
                                                    <i class="fa-solid fa-circle-check text-[10px] text-current opacity-40"></i>
                                                </div>
                                                <p class="text-xs font-black leading-tight truncate uppercase">
                                                    {{ $course->subject }}</p>
                                                <div class="mt-auto flex items-center justify-between">
                                                    <span class="text-[10px] font-bold opacity-70">{{ $course->room }}</span>
                                                    <span class="text-[9px] opacity-40 italic">{{ substr($course->start_time, 0, 5) }}</span>
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

    <!-- MODAL -->
    <div id="modalCours"
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl hidden z-[100] flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-white/10 w-full max-w-lg p-10 rounded-[2.5rem] shadow-3xl">
            <div class="flex justify-between items-center mb-10">
                <h3 id="modalTitle" class="text-xl font-black text-white uppercase tracking-tighter">Édition du cours</h3>
                <button type="button" onclick="document.getElementById('modalCours').classList.add('hidden')"
                    class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- FORM CREATE/UPDATE -->
            <form id="courseForm" action="{{ route('courses.store') }}" method="POST" class="space-y-6">
                @csrf
                <div id="methodField"></div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Matière</label>
                        <input type="text" name="subject" id="subject" required
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-indigo-500 outline-none transition-all font-bold">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Jour</label>
                        <select name="day" id="day"
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white font-bold">
                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Classe</label>
                        <select name="class_name" id="class_name"
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white font-bold">
                            <option value="">-- Sélectionner une classe --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Début</label>
                        <input type="time" name="start_time" id="start_time" required
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white font-bold">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Fin</label>
                        <input type="time" name="end_time" id="end_time" required
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white font-bold">
                    </div>

                    <div class="col-span-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Salle</label>
                        <select name="room" id="room" required
                            class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-white font-bold">
                            <option value="">-- Sélectionner une salle --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->name }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" id="submitBtn"
                        class="flex-1 bg-white text-black font-black py-5 rounded-2xl uppercase text-[11px] tracking-widest hover:bg-indigo-50 transition-all">
                        Enregistrer
                    </button>

                    <!-- DELETE BUTTON (NO NESTED FORM) -->
                    <button type="button" id="deleteBtn"
                        class="hidden bg-red-500/10 text-red-500 p-5 rounded-2xl hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </form>

            <!-- SEPARATE DELETE FORM -->
            <form id="deleteForm" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Nouveau Cours";
            document.getElementById('courseForm').action = "{{ route('courses.store') }}";
            document.getElementById('methodField').innerHTML = "";
            document.getElementById('courseForm').reset();

            // Hide delete
            document.getElementById('deleteBtn').classList.add('hidden');
            document.getElementById('deleteForm').classList.add('hidden');
            document.getElementById('deleteForm').action = "";

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

            // Setup delete (separate form)
            const deleteBtn = document.getElementById('deleteBtn');
            const deleteForm = document.getElementById('deleteForm');

            deleteForm.action = "/courses/" + course.id;
            deleteForm.classList.remove('hidden');

            deleteBtn.classList.remove('hidden');
            deleteBtn.onclick = () => {
                if (confirm('Supprimer ?')) {
                    deleteForm.submit();
                }
            };

            document.getElementById('modalCours').classList.remove('hidden');
        }
    </script>
</body>

</html>