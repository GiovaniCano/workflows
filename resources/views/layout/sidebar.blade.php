<aside id="sidebar" class="glass">
    <div>
        <header>
            <x-brand href="{{route('workflow.index')}}" />
        </header>

        <nav id="sidebar-content">
            <ul class="unstyled-list">
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, consectetur. Vero, suscipit veniam? Sit a deserunt unde quisquam eaque, eligendi ipsam, provident quaerat tempora, ratione aut consectetur perferendis aspernatur nostrum.</li>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, consectetur. Vero, suscipit veniam? Sit a deserunt unde quisquam eaque, eligendi ipsam, provident quaerat tempora, ratione aut consectetur perferendis aspernatur nostrum.</li>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, consectetur. Vero, suscipit veniam? Sit a deserunt unde quisquam eaque, eligendi ipsam, provident quaerat tempora, ratione aut consectetur perferendis aspernatur nostrum.</li>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, consectetur. Vero, suscipit veniam? Sit a deserunt unde quisquam eaque, eligendi ipsam, provident quaerat tempora, ratione aut consectetur perferendis aspernatur nostrum.</li>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, consectetur. Vero, suscipit veniam? Sit a deserunt unde quisquam eaque, eligendi ipsam, provident quaerat tempora, ratione aut consectetur perferendis aspernatur nostrum.</li>
            </ul>
        </nav>
    
        <footer>
            {{-- <button>User name</button> --}}
            <form action="{{route('logout')}}" method="POST">
                @csrf
                <input class="btn btn-primary align-center d-b" type="submit" value="{{__('Logout')}}">
            </form>
        </footer>
    </div>
</aside>