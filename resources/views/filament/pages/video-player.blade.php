@vite(['resources/css/app.css'])
<x-filament::section>
    <div class="p-4">
        @if ($url)
            <div x-data="{
                init() {
                    const video = this.$refs.player;
                    const videoSrc = '{{ $url }}';
                    if (typeof Hls !== 'undefined' && Hls.isSupported()) {
                        const hls = new Hls();
                        hls.loadSource(videoSrc);
                        hls.attachMedia(video);
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = videoSrc;
                    }
                }
            }">
                <video x-ref="player" class="w-full rounded-xl shadow-sm border border-gray-200" controls autoplay
                    playsinline></video>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        @else
            <div class="text-center p-10 text-gray-500">
                Không tìm thấy nguồn phát video.
            </div>
        @endif
    </div>
</x-filament::section>
