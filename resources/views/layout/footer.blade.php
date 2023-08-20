<footer id="footer">
    <div class="container">
        <nav>
            <ul class="unstyled-list m-r-1">
                {{-- <li>
                    <a href="{{route('landing-page')}}">{{__('Home')}}</a>
                </li> --}}
                {{-- <li>
                    <a href="{{route('pricing')}}">{{__('Pricing')}}</a>
                </li> --}}
                <li>
                    <form action="{{route('set-locale')}}" method="POST">
                        @csrf
                        <select name="locale" id="locale">
                            {{-- <option value="" selected style="display:none">{{__('Language')}}</option> --}}
                            <option value="en" @if(app()->getLocale() === 'en') selected @endif>English</option>
                            <option value="es" @if(app()->getLocale() === 'es') selected @endif>Español</option>
                        </select>
                    </form>
                </li>
                <li>
                    <a href="/sitemap.xml" target="_blank">{{__('Sitemap')}}</a>
                </li>
            </ul>

            <ul class="unstyled-list">
                <li>
                    <a href="{{route('privacy')}}">{{__('Privacy policy')}}</a>
                </li>
                <li>
                    <a href="{{route('terms')}}">{{__('Terms and conditions')}}</a>
                </li>
            </ul>
        </nav>
        <p class="text-center">
            © <?php echo date('Y'); ?> Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        </p>
    </div>
</footer>

<script>
    $('#locale').on('change', function() {
        $(this).parent().submit()
    })
</script>