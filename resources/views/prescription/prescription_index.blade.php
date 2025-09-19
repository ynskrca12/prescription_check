@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-3">
        <select id="branchSelect" class="form-select">
            <option value="">Branş Seçiniz</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>

        <select id="diagnosisSelect" class="form-select mt-2">
            <option value="">Tanı Kodu Seçiniz</option>
        </select>

        <select id="moleculeSelect" class="form-select mt-2">
            <option value="">Molekül Seçiniz</option>
        </select>
    </div>

    <div class="col-md-9">
        <div id="moleculeInfo">
            <h4 id="moleculeName"></h4>
            <p id="moleculeDescription"></p>
        </div>

        <div id="labRules" class="alert alert-info mt-3"></div>

        <div id="labInputs" class="mt-2"></div>

        <div id="prescriptionResult" class="mt-3"></div>
    </div>
</div>


@endsection

@push('scripts')
<script>
$(function() {
    const csrfToken = '{{ csrf_token() }}';

    // Branşa bağlı tanı kodlarını yükle
    function loadDiagnosis(branchId) {
        $('#diagnosisSelect').prop('disabled', true).html('<option>Yükleniyor...</option>');
        $.getJSON(`{{ url('ajax/prescription/diagnosis') }}/${branchId}`)
            .done(function(data) {
                let options = '<option value="">Tanı Kodu Seçiniz</option>';
                data.forEach(d => { options += `<option value="${d.id}">${d.code} - ${d.description}</option>`; });
                $('#diagnosisSelect').html(options).prop('disabled', false);
            });
    }

    // Tanıya bağlı molekülleri yükle
    function loadMolecules(diagnosisId) {
        $('#moleculeSelect').prop('disabled', true).html('<option>Yükleniyor...</option>');
        $.getJSON(`{{ url('ajax/prescription/molecules') }}/${diagnosisId}`)
            .done(function(data) {
                let options = '<option value="">Molekül Seçiniz</option>';
                data.forEach(d => { options += `<option value="${d.id}">${d.name}</option>`; });
                $('#moleculeSelect').html(options).prop('disabled', false);
            });
    }

    // Molekül seçildiğinde lab kurallarını yükle
    function loadLabRules(moleculeId) {
        $.getJSON(`{{ url('ajax/prescription/labrules') }}/${moleculeId}`)
            .done(function(rules) {
                let htmlRules = '';
                let htmlInputs = '';
                rules.forEach(rule => {
                    htmlRules += `<div>${rule.parameter.name} ${rule.operator} ${rule.value} ${rule.parameter.unit}</div>`;
                    htmlInputs += `
                        <div class="mb-2">
                            <label>${rule.parameter.name} (${rule.parameter.unit})</label>
                            <input type="number" class="form-control labValue" data-param="${rule.laboratory_parameter_id}">
                        </div>
                    `;
                });
                $('#labRules').html(htmlRules);
                $('#labInputs').html(htmlInputs);
                $('#prescriptionResult').html('');
            });
    }

    // Reçete uygunluğunu kontrol et
    function checkEligibility(moleculeId) {
        const labValues = {};
        $('.labValue').each(function() {
            labValues[$(this).data('param')] = parseFloat($(this).val());
        });

        $.ajax({
            url: `{{ url('ajax/prescription/check') }}/${moleculeId}`,
            type: 'POST',
            data: { _token: csrfToken, lab_values: labValues },
            success: function(res) {
                const alertClass = res.eligible ? 'alert-success' : 'alert-danger';
                $('#prescriptionResult').html(`<div class="alert ${alertClass}">${res.messages.join('<br>')}</div>`);
            }
        });
    }

    // Event listeners
    $('#branchSelect').on('change', function() {
        const branchId = $(this).val();
        $('#diagnosisSelect, #moleculeSelect').html('<option>Seçiniz</option>');
        $('#labRules, #labInputs, #prescriptionResult').html('');
        if(branchId) loadDiagnosis(branchId);
    });

    $('#diagnosisSelect').on('change', function() {
        const diagId = $(this).val();
        console.log('tanı kod->' + diagId);
        $('#moleculeSelect').html('<option>Seçiniz</option>');
        $('#labRules, #labInputs, #prescriptionResult').html('');
        if(diagId) loadMolecules(diagId);
    });

    $('#moleculeSelect').on('change', function() {
        const molId = $(this).val();
        $('#labRules, #labInputs, #prescriptionResult').html('');
        if(molId) loadLabRules(molId);
    });

    $(document).on('input', '.labValue', function() {
        const molId = $('#moleculeSelect').val();
        if(molId) checkEligibility(molId);
    });
});
</script>
@endpush
