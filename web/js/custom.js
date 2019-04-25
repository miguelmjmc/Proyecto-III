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

    $(document).on('click', '.rif', function () {
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

    $(document).on('click', '.ci', function () {
        $(this).mask('A-00000000', {
            translation: {
                'A': {pattern: /[ve]/i},
                '0': {pattern: /[0-9]/}
            }
            ,onKeyPress: function (value, event) {
                event.currentTarget.value = value.toUpperCase();
            }
        });
    });

    $(document).on('click', '.phone', function () {
        $(this).mask('(0000) 000-0000');
    });

    $(document).on('click', '.money', function () {
        $(this).maskMoney();
    });

    $('.btn-new').appendTo('.add-btn-container');

    $(".alert").fadeTo(2000, 500).slideUp(500, function(){
        $(".alert").slideUp(500);
    });

    $('.datepicker').datepicker({
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        todayHighlight: true
    });

});
