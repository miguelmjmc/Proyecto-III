function getLineChartConfig(data) {
    return {
        type: 'line',
        data: data,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                mode: 'index',
                intersect: false
            }
        }
    };
}

function getDoughnutChartConfig(data) {
    return {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    }
}

$(document).ready(function () {

    var url = $('#nav-active').data('url');
    $('a[href="' + url + '"]').parents('li').addClass('active');
    $('.treeview[class="active"]').addClass('menu-open');
    $('.active > .treeview-menu').show();

    $('.alert').fadeTo(2000, 500).slideUp(500, function () {
        $('.alert').slideUp(1000);
    });

    $(document).on('click', '.calculator-trigger', function (e) {
        e.preventDefault();

        $('#calculator').modal('show');
    });


    $('.under-construction').click(function (e) {
        e.preventDefault();

        alert('Esta caracteristica se encuentra en construcción. Disculpe las molestias.');
    });

    $(document).on('focus', '.rif', function () {
        $(this).mask('A-00000000-0', {
            translation: {
                'A': {pattern: /[vepgjc]/i},
                '0': {pattern: /[0-9]/}
            }
            ,onKeyPress: function (value, event) {
                event.currentTarget.value = value.toUpperCase();
            }
        });
    });

    $(document).on('focus', '.ci', function () {
        $(this).mask('V-00000000', {
            translation: {
                '0': {pattern: /[0-9]/}
            }
            ,onKeyPress: function (value, event) {
                event.currentTarget.value = value.toUpperCase();
            }
        });
    });

    $(document).on('focus', '.phone', function () {
        $(this).mask('(0000) 000-0000');
    });

    $(document).on('focus', '.money', function () {
        $(this).maskMoney();
    });

    $(document).on('focus', '.number', function () {
        $(this).autoNumeric();
    });


    $('.btn-new').appendTo('.add-btn-container');

    $(".alert").fadeTo(2000, 500).slideUp(500, function(){
        $(".alert").slideUp(500);
    });

    $('.selectpicker').selectpicker();

    $(document).on('focus', 'form:not([readonly]) .datepicker', function () {
        $(this).datepicker({
            format: 'yyyy/mm/dd',
            language: 'es',
            autoclose: true,
            todayHighlight: true
        });
    });

});
