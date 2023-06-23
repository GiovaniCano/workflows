@push('templates')

{{-- modal --}}
<script id="modal-template" type="text/template">
    <x-modal/>
</script>

{{-- sidebar element --}}
<script id="sidebar-el-template" type="text/template">
    <x-workflows.sidebar-el :section="$templatesModels['section']" :maxlen="15" />
</script>

{{-- section --}}
    {{-- .section-main | .section-nested --}}
<script id="section-template" type="text/template">
    <x-workflows.section-form :section="$templatesModels['section']" class="section-form js-action-target" />
</script>

{{-- mini section --}}
<script id="minisection-btn-template" type="text/template">
    <x-workflows.mini-section-btn :section="$templatesModels['section']" class="js-action-target" />
</script>
<script id="minisection-modal-template" type="text/template">
    <x-modal data-minisection-modal-id="" class="modal-mini-section js-action-target">
        <x-workflows.section-form :section="$templatesModels['minisection']" class="section-mini section-form" />
    </x-modal>
</script>

{{-- wysiwyg --}}
<script id="wysiwyg-template" type="text/template">
    <x-workflows.wysiwyg-form :wysiwyg="$templatesModels['wysiwyg']" />
</script>

{{-- image --}}
<script id="image-template" type="text/template">
    <x-workflows.image :image="$templatesModels['image']" class="js-action-target" />
</script>

@endpush