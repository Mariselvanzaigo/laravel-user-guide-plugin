<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
@if(Session::has('success'))
  toastr.success('{{ Session::get('success') }}');
@endif
@if(Session::has('error'))
  toastr.error('{{ Session::get('error') }}');
@endif
</script>
