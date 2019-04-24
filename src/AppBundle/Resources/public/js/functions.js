if (typeof jQuery === 'undefined') {
    throw new Error('JQuery is required');
}

!function (a) {
    a.fn.datepicker.dates.es = {
        days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        daysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        daysMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        today: 'Hoy',
        monthsTitle: 'Meses',
        clear: 'Borrar',
        weekStart: 1,
        format: 'dd/mm/yyyy'
    }
}(jQuery);

function dateFilter(settings, json, dateColumn) {

    var table = settings.oInstance.api();

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            if ($('#startDate').val() === '' && $('#endDate').val() === '') {
                return true;
            }

            if ($('#startDate').val() !== '' || $('#endDate').val() !== '') {
                var iMin_temp = $('#startDate').val();
                if (iMin_temp === '') {
                    iMin_temp = '0/0/0';
                }

                var iMax_temp = $('#endDate').val();
                if (iMax_temp === '') {
                    iMax_temp = '9999/99/99';
                }

                var arr_min = iMin_temp.split('/');
                var arr_max = iMax_temp.split('/');
                var arr_date = data[dateColumn].split('/');
                var aux = arr_date[2].split(' ');
                arr_date[2] = aux[0];

                var iMin = new Date(arr_min[0], arr_min[1], arr_min[2], 0, 0, 0, 0);
                var iMax = new Date(arr_max[0], arr_max[1], arr_max[2], 0, 0, 0, 0);
                var iDate = new Date(arr_date[0], arr_date[1], arr_date[2], 0, 0, 0, 0);

                if (iMin === '' && iMax === '') {
                    return true;
                }
                else if (iMin === '' && iDate < iMax) {
                    return true;
                }
                else if (iMin <= iDate && '' === iMax) {
                    return true;
                }
                else if (iMin <= iDate && iDate <= iMax) {
                    return true;
                }

                return false;
            }
        }
    );

    $('#startDate').datepicker({
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#endDate').datepicker('setStartDate', minDate);
    });

    $('#endDate').datepicker({
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#startDate').datepicker('setEndDate', maxDate);
    });

    $('#startDate, #endDate').on('changeDate', function () {
        table.draw();
    });
}

var sumoselectConfig = {
    placeholder: 'Seleccione...'
};

$(document).ready(function () {

    $(document).on('mousedown', 'select[readonly]', function (e) {
        e.preventDefault();
    });

    //================================================================================
    //================================================================================
    // Call modal
    //================================================================================
    $(document).on('click', '.modal-trigger', function (event) {
        event.preventDefault();

        $('#modal-default').remove();
        $('body').append('<div id="modal-default" class="modal fade" role="dialog"></div>');

        $.ajax({
            url: $(this).data('action'),
            type: $(this).data('method'),
            success: function (data) {
                $('#modal-default').empty().append(data).modal('show');
            }
        });
    });

    $('#modal-default').on('hidden.bs.modal', function () {
        $(this).remove();
    });

    //================================================================================
    //================================================================================
    // Submit form
    //================================================================================
    $(document).on('click', '.ajax-submit', function (e) {
        e.preventDefault();

        $('#ajax-submit').prop('disabled', true);
        $('.modal-container').hide();
        $('.loader').show();

        var form = $(this).parents('form');

        if (null === $(this).find('input:file')) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (data) {
                    if ('success' === data) {
                        location.reload();
                    } else {
                        $('#modal-default').empty().append(data);
                    }
                }
            });
        } else {
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type: form.attr('method'),
                url: form.attr('action'),
                data: new FormData($(form)[0]),
                success: function (data) {
                    if ('success' === data) {
                        location.reload();
                    } else {
                        $('#modal-default').empty().append(data);
                    }
                }
            });
        }
    });
});


var lang = {
    sProcessing: 'Procesando...',
    sLengthMenu: 'Mostrar _MENU_ registros',
    sZeroRecords: 'No se encontraron resultados',
    sEmptyTable: 'Ningún dato disponible en esta tabla',
    sInfo: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
    sInfoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
    sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
    sInfoPostFix: '',
    sSearch: 'Buscar:',
    sUrl: '',
    sInfoThousands: ',',
    sLoadingRecords: 'Cargando...',
    oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior'
    },
    oAria: {
        sSortAscending: ': Activar para ordenar la columna de manera ascendente',
        sSortDescending: ': Activar para ordenar la columna de manera descendente'
    },
    buttons: {
        print: 'Imprimir',
        copy: "Copiar",
        copyTitle: 'Añadido al portapapeles',
        copyKeys: 'Presione <i>ctrl</i> o <i>\u2318</i> + <i>C</i> para copiar los datos de la tabla al portapapeles. <br><br>Para cancelar, haga clic en este mensaje o presione Esc.',
        copySuccess: {
            _: '%d lineas copiadas',
            1: '1 linea copiada'
        }
    },
    select: {
        rows: {
            _: '%d filas seleccionadas',
            1: '1 fila seleccionada'
        }
    }
};

$(document).ready(function () {
    $('.datatable').each(function () {

        var selector = $(this);
        var table = null;

        $.ajax({
            type: 'GET',
            url: $(this).data('src'),
            success: function (data) {
                table = selector.DataTable({
                    order: data['order'],
                    columns: data['columns'],
                    data: data['data'],
                    language: lang,
                    responsive: true,
                    dom: "<'row'<'col-sm-8 float-right-content'B><'col-sm-4'<'btn-add-container'>><'col-sm-12 date-filter-container'><'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                    select: {
                        style: 'os',
                        selector: 'td:not(:last-child)',
                        blurable: true
                    },
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
                });
            }
        });
    });
});

$(document).ready(function () {
    $('.datatable-filter').each(function () {

        var selector = $(this);
        var table = null;

        $.ajax({
            type: 'GET',
            url: $(this).data('src'),
            success: function (data) {
                table = selector.DataTable({
                    order: data['order'],
                    columns: data['columns'],
                    data: data['data'],
                    language: lang,
                    responsive: true,
                    initComplete: function (settings, json) {
                        dateFilter(settings, json, 0);
                    },
                    dom: "<'row'<'col-sm-8 float-right-content'B><'col-sm-4'<'btn-add-container'>><'col-sm-12 date-filter-container'><'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                    select: {
                        style: 'os',
                        selector: 'td:not(:last-child)',
                        blurable: true
                    },
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
                });
            }
        });
    });
});
