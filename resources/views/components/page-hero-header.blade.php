@props(['title'])

<div class="relative rounded-xl overflow-hidden group mb-4">
    <!-- Dynamic height with parallax effect -->
    <div class="h-32">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/60 to-black/80 opacity-30"></div>
    </div>

    <!-- Content container -->
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <!-- Animated decorative elements -->
        <div class="relative w-full max-w-4xl mx-auto px-4">
            <div class="absolute top-1/2 w-full h-px bg-gradient-to-r from-transparent via-white/30 to-transparent transform -translate-y-4 transition-all duration-700"></div>
            <div class="absolute top-1/2 w-full h-px bg-gradient-to-r from-transparent via-white/30 to-transparent transform translate-y-4 transition-all duration-700"></div>

            <!-- Main title with modern typography -->
            <h1 class="relative text-4xl md:text-6xl lg:text-7xl font-bold text-white text-center tracking-wider">
               <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-white/90 to-white hover:from-white hover:via-white/80 hover:to-white/90 transition-all duration-300">
                   {!! $title !!}
               </span>
            </h1>
        </div>
    </div>
</div>