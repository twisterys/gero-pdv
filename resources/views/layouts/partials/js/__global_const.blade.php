<script>
    const __balises_select2_route = "{{route('balises.select')}}";
    const __fournisseur_select2_route = "{{route('fournisseurs.select') }}";
    const famille_select_ajax_link ='{{route('familles.select') }}';
    const unite_select_ajax_link = '{{ route('unites.select') }}';
    const tax_select_ajax_link = '{{ route('taxes.select') }}';
    const __comercial_select2_route = "{{ route('commercials.select') }}";
    const __livraison_select2_route = "{{ route('livraison.select') }}";
    const __client_select2_route = "{{ route('clients.select') }}";
    const __exercice_start_date = "{{Carbon\Carbon::now()->setYear(session()->get('exercice'))->firstOfYear()->format('d/m/Y')}}"
    const __exercice_end_date = "{{Carbon\Carbon::now()->setYear(session()->get('exercice'))->lastOfYear()->format('d/m/Y')}}"
</script>
