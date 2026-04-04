<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team | ARTHADATA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #fcfcfc;
            /* Motif Grid Halus */
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .glass-card:hover {
            transform: scale(1.01);
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="text-slate-800 antialiased">

    <div class="max-w-4xl mx-auto px-6 py-24">
        <header class="mb-20">
            <div class="flex items-center gap-3 mb-2">
                <svg width="44" height="36" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-emerald-800 fill-current">
                    <path d="M5 17.2C3.61667 17.2 2.4375 16.7125 1.4625 15.7375C0.4875 14.7625 0 13.5833 0 12.2C0 10.8167 0.4875 9.6375 1.4625 8.6625C2.4375 7.6875 3.61667 7.2 5 7.2C6.38333 7.2 7.5625 7.6875 8.5375 8.6625C9.5125 9.6375 10 10.8167 10 12.2C10 13.5833 9.5125 14.7625 8.5375 15.7375C7.5625 16.7125 6.38333 17.2 5 17.2ZM5 13.7C4.58333 13.7 4.22917 13.5542 3.9375 13.2625C3.64583 12.9708 3.5 12.6167 3.5 12.2C3.5 11.7833 3.64583 11.4292 3.9375 11.1375C4.22917 10.8458 4.58333 10.7 5 10.7C5.41667 10.7 5.77083 10.8458 6.0625 11.1375C6.35417 11.4292 6.5 11.7833 6.5 12.2C6.5 12.6167 6.35417 12.9708 6.0625 13.2625C5.77083 13.5542 5.41667 13.7 5 13.7ZM18.5 17.2C17.5333 17.2 16.7083 16.8583 16.025 16.175C15.3417 15.4917 15 14.6667 15 13.7C15 12.7333 15.3417 11.9083 16.025 11.225C16.7083 10.5417 17.5333 10.2 18.5 10.2C19.4667 10.2 20.2917 10.5417 20.975 11.225C21.6583 11.9083 22 12.7333 22 13.7C22 14.6667 21.6583 15.4917 20.975 16.175C20.2917 16.8583 19.4667 17.2 18.5 17.2ZM3 6.2C2.71667 6.2 2.47917 6.10417 2.2875 5.9125C2.09583 5.72083 2 5.48333 2 5.2C2 4.91667 2.09583 4.67917 2.2875 4.4875C2.47917 4.29583 2.71667 4.2 3 4.2H6C6.55 4.2 7.02083 4.39583 7.4125 4.7875C7.80417 5.17917 8 5.65 8 6.2H3ZM5 15.2C5.83333 15.2 6.54167 14.9083 7.125 14.325C7.70833 13.7417 8 13.0333 8 12.2C8 11.3667 7.70833 10.6583 7.125 10.075C6.54167 9.49167 5.83333 9.2 5 9.2C4.16667 9.2 3.45833 9.49167 2.875 10.075C2.29167 10.6583 2 11.3667 2 12.2C2 13.0333 2.29167 13.7417 2.875 14.325C3.45833 14.9083 4.16667 15.2 5 15.2ZM18.5 15.2C18.9167 15.2 19.2708 15.0542 19.5625 14.7625C19.8542 14.4708 20 14.1167 20 13.7C20 13.2833 19.8542 12.9292 19.5625 12.6375C19.2708 12.3458 18.9167 12.2 18.5 12.2C18.0833 12.2 17.7292 12.3458 17.4375 12.6375C17.1458 12.9292 17 13.2833 17 13.7C17 14.1167 17.1458 14.4708 17.4375 14.7625C17.7292 15.0542 18.0833 15.2 18.5 15.2ZM10.9 13.2H14.05C14.2167 12 14.7375 11.0417 15.6125 10.325C16.4875 9.60833 17.475 9.25 18.575 9.25C18.9917 9.25 19.4042 9.30833 19.8125 9.425C20.2208 9.54167 20.6167 9.71667 21 9.95V5.2C21 4.65 20.8042 4.17917 20.4125 3.7875C20.0208 3.39583 19.55 3.2 19 3.2H12.7L11.65 2.1L13.05 0.7L12.35 0L8.8 3.55L9.55 4.25L10.95 2.85L12 3.9V6.2C12 6.75 11.8042 7.22083 11.4125 7.6125C11.0208 8.00417 10.55 8.2 10 8.2H9.45C9.95 8.75 10.3292 9.36667 10.5875 10.05C10.8458 10.7333 10.975 11.45 10.975 12.2C10.975 12.3667 10.9708 12.5333 10.9625 12.7C10.9542 12.8667 10.9333 13.0333 10.9 13.2Z" fill="currentColor"/>
                </svg>

                <h1 class="text-4xl tracking-tighter">
                    <span class="font-[900] text-black">ARTHA</span><span class="font-light text-slate-400 ml-1">DATA</span>
                </h1>
            </div>
            <div class="h-1 w-12 bg-emerald-500 rounded-full mb-6"></div>
            <p class="text-slate-500 font-medium max-w-md leading-relaxed">
                Berkenalan dengan rekan-rekan di ARTHADATA
            </p>
        </header>

        <div class="space-y-8">
            @foreach($members as $member)
            <div class="glass-card group border border-slate-100 rounded-[2rem] p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    
                    <div class="relative flex-none">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl overflow-hidden bg-slate-100 border-4 border-white shadow-sm transition-transform group-hover:rotate-3">
                            @if(isset($member->photo) && $member->photo != "")
                                <img src="{{ str_starts_with($member->photo, 'http') ? $member->photo : asset('assets/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400 text-3xl font-bold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center gap-2 mb-4">
                            <h2 class="text-2xl font-extrabold text-black tracking-tight">
                                {{ $member->name }}
                            </h2>
                            <div class="flex justify-center md:justify-start gap-3 items-center">
                                <span class="hidden md:block w-4 h-px bg-slate-200"></span>
                                <a href="{{ $member->linkedin }}" target="_blank" class="text-slate-300 hover:text-[#0077b5] transition-all">
                                    <i class="fab fa-linkedin text-xl"></i>
                                </a>
                            </div>
                        </div>

                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-[0.2em] mb-6">
                            {{ $member->role }}
                        </p>

                        <div class="flex flex-wrap justify-center md:justify-start gap-3">
                            @forelse($member->portfolios as $porto)
                            <a href="{{ $porto->type == 'pdf' ? asset('assets/' . $porto->link) : $porto->link }}" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-slate-100 rounded-xl text-xs font-bold text-slate-600 hover:border-black hover:text-black transition-all shadow-sm">
                                <i class="{{ $porto->type == 'pdf' ? 'far fa-file-pdf text-red-400' : 'fas fa-link text-blue-400' }} mr-2"></i>
                                {{ $porto->title }}
                            </a>
                            @empty
                            <span class="text-[10px] text-slate-300 uppercase tracking-widest italic">No assets</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <footer class="mt-24 text-center">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em]">© 2026 ArthaData</p>
        </footer>
    </div>

</body>
</html>