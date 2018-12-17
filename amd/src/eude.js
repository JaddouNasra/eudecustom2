// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/* jshint node: true, browser: false */
/* eslint-env node */

/**
 * Javascript used in eudecustom local plugin.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'jqueryui'], function ($) {
    return {
        message: function () {
            $('#menucategoryname').change(function () {
                $('#menucategoryname option:selected').each(function () {
                    var catId = $('#menucategoryname').val();
                    $('#menucoursename').empty();
                    $('#menucoursename').append("<option value=''>-- Módulo --</option>");
                    $('#menudestinatarioname').empty();
                    $('#menudestinatarioname').append("<option value=''>-- Destinatario --</option>");
                    $.ajax({
                        data: 'catId :' + catId,
                        url: 'eudemessagesrequest.php?messagecat=' + catId,
                        type: 'get',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#menucoursename').append(response);
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            });
            $('#menucoursename').change(function () {
                $('#menucoursename option:selected').each(function () {
                    var catId = $('#menucoursename').val();
                    $('#menudestinatarioname').empty();
                    $('#menudestinatarioname').append("<option value=''>-- Destinatario --</option>");
                    $.ajax({
                        data: 'catId :' + catId,
                        url: 'eudemessagesrequest.php?messagecourse=' + catId,
                        type: 'get',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#menudestinatarioname').append(response);
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            });
        },
        matriculation: function () {
            var es = {
                closeText: 'Cerrar',
                prevText: '<Ant',
                nextText: 'Sig>',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: [
                    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.regional.es = es;
            $.datepicker.setDefaults($.datepicker.regional.es);
            $('.date1').first().change(function () {
                if (window.confirm('¿Calcular las fechas siguientes automáticamente?')) {
                    var initialDate1 = new Date($(this).datepicker('getDate'));

                    var initialDate2 = new Date(initialDate1.getFullYear(),
                            initialDate1.getMonth(),
                            initialDate1.getDate() + 7);
                    var initialDate3 = new Date(initialDate2.getFullYear(),
                            initialDate2.getMonth(),
                            initialDate2.getDate() + 7);
                    var initialDate4 = new Date(initialDate3.getFullYear(),
                            initialDate3.getMonth(),
                            initialDate3.getDate() + 7);
                    var dateposition = 2;
                    $('.date1').not(':first').each(function () {
                        switch (dateposition) {
                            case 1:
                                $(this).datepicker('setDate', initialDate1);
                                dateposition++;
                                break;
                            case 2:
                                $(this).datepicker('setDate', initialDate2);
                                dateposition++;
                                break;
                            case 3:
                                $(this).datepicker('setDate', initialDate3);
                                dateposition++;
                                break;
                            case 4:
                                $(this).datepicker('setDate', initialDate4);
                                dateposition = 1;
                                break;
                            default:

                        }
                    });
                }
            });
            $('.date2').first().change(function () {
                if (window.confirm('¿Calcular las fechas siguientes automáticamente?')) {
                    var initialDate1 = new Date($(this).datepicker('getDate'));

                    var initialDate2 = new Date(initialDate1.getFullYear(),
                            initialDate1.getMonth(),
                            initialDate1.getDate() + 7);
                    var initialDate3 = new Date(initialDate2.getFullYear(),
                            initialDate2.getMonth(),
                            initialDate2.getDate() + 7);
                    var initialDate4 = new Date(initialDate3.getFullYear(),
                            initialDate3.getMonth(),
                            initialDate3.getDate() + 7);
                    var dateposition = 2;
                    $('.date2').not(':first').each(function () {
                        switch (dateposition) {
                            case 1:
                                $(this).datepicker('setDate', initialDate1);
                                dateposition++;
                                break;
                            case 2:
                                $(this).datepicker('setDate', initialDate2);
                                dateposition++;
                                break;
                            case 3:
                                $(this).datepicker('setDate', initialDate3);
                                dateposition++;
                                break;
                            case 4:
                                $(this).datepicker('setDate', initialDate4);
                                dateposition = 1;
                                break;
                            default:

                        }
                    });
                }
            });
            $('.date3').first().change(function () {
                if (window.confirm('¿Calcular las fechas siguientes automáticamente?')) {
                    var initialDate1 = new Date($(this).datepicker('getDate'));

                    var initialDate2 = new Date(initialDate1.getFullYear(),
                            initialDate1.getMonth(),
                            initialDate1.getDate() + 7);
                    var initialDate3 = new Date(initialDate2.getFullYear(),
                            initialDate2.getMonth(),
                            initialDate2.getDate() + 7);
                    var initialDate4 = new Date(initialDate3.getFullYear(),
                            initialDate3.getMonth(),
                            initialDate3.getDate() + 7);
                    var dateposition = 2;
                    $('.date3').not(':first').each(function () {
                        switch (dateposition) {
                            case 1:
                                $(this).datepicker('setDate', initialDate1);
                                dateposition++;
                                break;
                            case 2:
                                $(this).datepicker('setDate', initialDate2);
                                dateposition++;
                                break;
                            case 3:
                                $(this).datepicker('setDate', initialDate3);
                                dateposition++;
                                break;
                            case 4:
                                $(this).datepicker('setDate', initialDate4);
                                dateposition = 1;
                                break;
                            default:

                        }
                    });
                }
            });
            $('.date4').first().change(function () {
                if (window.confirm('¿Calcular las fechas siguientes automáticamente?')) {
                    var initialDate1 = new Date($(this).datepicker('getDate'));

                    var initialDate2 = new Date(initialDate1.getFullYear(),
                            initialDate1.getMonth(),
                            initialDate1.getDate() + 7);
                    var initialDate3 = new Date(initialDate2.getFullYear(),
                            initialDate2.getMonth(),
                            initialDate2.getDate() + 7);
                    var initialDate4 = new Date(initialDate3.getFullYear(),
                            initialDate3.getMonth(),
                            initialDate3.getDate() + 7);
                    var dateposition = 2;
                    $('.date4').not(':first').each(function () {
                        switch (dateposition) {
                            case 1:
                                $(this).datepicker('setDate', initialDate1);
                                dateposition++;
                                break;
                            case 2:
                                $(this).datepicker('setDate', initialDate2);
                                dateposition++;
                                break;
                            case 3:
                                $(this).datepicker('setDate', initialDate3);
                                dateposition++;
                                break;
                            case 4:
                                $(this).datepicker('setDate', initialDate4);
                                dateposition = 1;
                                break;
                            default:

                        }
                    });
                }
            });
            $('.inputdate').datepicker({dateFormat: 'dd/mm/yy'}).val();
            $('.inputdate').each(function () {
                var checkDate = new Date($(this).datepicker('getDate'));
                var minDate = new Date($(this).datepicker('option', 'minDate'));
                if (checkDate.getFullYear() == minDate.getFullYear() &&
                        checkDate.getMonth() == minDate.getMonth() &&
                        checkDate.getDate() == minDate.getDate()) {
                    $(this).datepicker('setDate', null);
                }
            });
            $('#resetfechas').click(function (e) {
                e.preventDefault();
                $('.inputdate').each(function () {
                    $(this).datepicker('setDate', null);
                });
            });
            $('#savedates').click(function (e) {
                var fieldNull = false;
                $('.inputdate').each(function () {
                    if (!$(this).val()) {
                        fieldNull = true;
                    }
                    var datewithslash = $(this).val();
                    var datewithdash = datewithslash.replace(new RegExp('/', 'g'), '-');
                    $(this).val(datewithdash);
                });
                if (fieldNull) {
                    e.preventDefault();
                    var text1 = 'Hay campos incorrectos';
                    var text2 = 'Rellene correctamente todos los campos';
                    window.alert(text1 + '. ' + text2);
                }
            });
        },
        academic: function () {
            $('#menucoursename').hide();
            $('#menustudentname').hide();
            $('#usergrades').hide();
            $('#menucategoryname').change(function () {
                $('#menucategoryname option:selected').each(function () {
                    var catId = $('#menucategoryname').val();
                    $('#menucoursename').empty();
                    $('#menucoursename').append("<option value=''>-- Módulo --</option>");
                    if (!$(this).val()) {
                        $('#menucoursename').hide();
                    } else {
                        $('#menucoursename').show();
                    }
                    $('#menustudentname').empty();
                    $('#menustudentname').append("<option value=''>-- Alumno --</option>");
                    $('#menustudentname').hide();
                    $('#usergrades').hide();
                    $.ajax({
                        data: 'catId :' + catId,
                        url: 'eudegradesearchrequest.php?cat=' + catId,
                        type: 'get',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#menucoursename').append(response);
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            });
            $('#menucoursename').change(function () {
                $('#menustudentname').show();
                $('#usergrades').hide();
                $("#menucoursename option:selected").each(function () {
                    if (!$(this).val()) {
                        $('#menustudentname').hide();
                    } else {
                        $('#menustudentname').show();
                    }
                    $('#menustudentname').empty();
                    $('#menustudentname').append("<option value=''>-- Alumno --</option>");
                    var courseId = $('#menucoursename').val();
                    $.ajax({
                        data: 'courseId :' + courseId,
                        url: 'eudegradesearchrequest.php?course=' + courseId,
                        type: 'get',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#menustudentname').append(response);
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            });
            $('#menustudentname').change(function () {
                $('#usergrades').show();
                if (!$(this).val()) {
                    $('#usergrades').hide();
                }
                $("#menustudentname option:selected").each(function () {
                    var studentId = $('#menustudentname').val();
                    var link = '../../grade/report/user/index.php?userid=' + studentId + '&id=' + $(
                            '#menucoursename').val();
                    $('#usergrades').attr('href', link);
                });
            });
        },
        calendar: function () {
            $('#modalwindowforprint').empty();
            $('#openmodalwindowforprint').click(function () {
                $('#modalwindowforprint').empty();
                $.ajax({
                    url: 'eudecalendarmodalwindow.php',
                    type: 'get',
                    success: function (response, status, thrownerror) {
                        try {
                            $('#modalwindowforprint').append(response);
                            $('#closemodalwindowbutton').click(function () {
                                $('#modalwindowforprint').empty();
                            });
                            var es = {
                                closeText: 'Cerrar',
                                prevText: '<Ant',
                                nextText: 'Sig>',
                                currentText: 'Hoy',
                                monthNames: [
                                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                monthNamesShort: [
                                    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                                ],
                                dayNames: [
                                    'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                                weekHeader: 'Sm',
                                dateFormat: 'dd/mm/yy',
                                firstDay: 1,
                                isRTL: false,
                                showMonthAfterYear: false,
                                yearSuffix: ''
                            };
                            $.datepicker.regional.es = es;
                            $.datepicker.setDefaults($.datepicker.regional.es);
                            $('.inputdate').datepicker({dateFormat: 'dd/mm/yy'}).val();
                        } catch (ex) {
                            window.console.log(ex.message);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }

                    },
                    error: function (jqXHR, status, thrownerror) {
                        window.console.log(jqXHR.responseText);
                        window.console.log(status);
                        window.console.log(thrownerror);
                    }
                });
            });
            $('.cb-eventkey').each(function () {
                $(this).click(function () {
                    var type = $(this).prop('name');
                    if ($(this).prop('checked')) {
                        $('div.' + type).each(function () {
                            $(this).removeClass('disabled' + type);
                        });
                    } else {
                        $('div.' + type).each(function () {
                            $(this).addClass('disabled' + type);
                        });
                    }
                });
            });
            $('div.hasevent').each(function () {
                var name = $(this).attr('class');
                if (name.includes("eudeevent")) {
                    $(this).parent().find("div[name='eudeglobalevent']").append($(this));
                }
                if (name.includes("intensivemodulebegin")) {
                    $(this).parent().find("div[name='intensivecourse']").append($(this));
                }
                if (name.includes("testdate")) {
                    $(this).parent().find("div[name='testsubmission']").append($(this));
                }
                if (name.includes("questionnairedate")) {
                    $(this).parent().find("div[name='questionnairedate']").append($(this));
                }
                if (name.includes("activityend")) {
                    $(this).parent().find("div[name='activitysubmission']").append($(this));
                }
                if (name.includes("modulebegin")) {
                    $(this).parent().find("div[name='normalcourse']").append($(this));
                }
            });
            $('div.hasevent').on( "mouseenter", function() {
                $(this).append("<div class='uep-wrap modal-dialog modal show modal-dialog modal-content eudecalendarpopup'>" +
                                    "<div class='uep-header header modal-header'>" +
                                        "<h3 class='modal-title' style='padding: 5px;'>Info</h3>" +
                                    "</div>" +
                                    "<div class='uep-content modal-body'>" +
                                        $(this).attr('data-core_calendar-popupcontent') +
                                     "</div>" +
                                "</div>");
            });
            $('div.hasevent').on( "mouseleave", function() {
                $(this).find('.eudecalendarpopup').remove();
            });
        },
        eventlist: function () {
            $('#printeventbutton').click(function () {
                $('.datepickerwrapper').hide();
                $('#generateeventlist').hide();
                $('#printeventbutton').hide();
                $('#page-footer').hide();
                $('#moodle-footer').hide();
                $('#mr-nav').hide();
                window.print();

                $('.contentwrapper').show();
                $('#printeventbutton').show();
                $('#page-footer').show();
                $('#moodle-footer').show();
                $('#mr-nav').show();
            });
            var es = {
                closeText: 'Cerrar',
                prevText: '<Ant',
                nextText: 'Sig>',
                currentText: 'Hoy',
                monthNames: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: [
                    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                ],
                dayNames: [
                    'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.regional.es = es;
            $.datepicker.setDefaults($.datepicker.regional.es);
            $('.inputdate').datepicker({dateFormat: 'dd/mm/yy'}).val();
            $('#generateeventlist').click(function (e) {
                var fieldNull = false;
                $('.inputdate').each(function () {
                    if (!$(this).val()) {
                        fieldNull = true;
                    }
                });
                if (fieldNull) {
                    e.preventDefault();
                    var text1 = 'Hay campos incorrectos';
                    var text2 = 'Rellene correctamente todos los campos';
                    window.alert(text1 + '. ' + text2);
                }
            });
        },
        profile: function () {
            function modalAction() {
                var idcourse;
                $('.letpv_abrir').click(function () {
                    var params = $(this).attr('id');
                    var studentid = $('#hiddenuserid').attr('value');

                    idcourse = params.substring(params.indexOf('(') + 1, params.indexOf(','));

                    var tpv = params.substring(params.indexOf(',') + 1,params.lastIndexOf(','));

                    var accion = params.substring(params.lastIndexOf(',') + 1,params.indexOf(')'));
                    $('#letpv_ventana-flotante').css('display', 'block');
                    $.ajax({
                        data: 'idcourse=' + idcourse,
                        url: 'eudemodaladvise.php?studentid=' + studentid,
                        type: 'post',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#letpv_ventana-flotante').html(response);
                                $('button.letpv_cerrar').click(function () {
                                    $('#letpv_ventana-flotante').css('display', 'none');
                                });
                                $('#letpv_course').attr('value', idcourse);
                                $('input.letpv_btn')
                                        .attr('id', 'abrirFechas(' + idcourse + ',' + tpv + ',' + accion + ')');

                                $('input.letpv_btn').click(function () {
                                    $('#letpv_ventana-flotante').css('display', 'block');
                                    $.ajax({
                                        data: 'idcourse=' + idcourse,
                                        url: 'eudemodaldates.php?studentid=' + studentid,
                                        type: 'post',
                                        success: function (response) {
                                            $('#letpv_ventana-flotante').html(response);
                                            $("button.letpv_cerrar").click(function () {
                                                $('#letpv_ventana-flotante').css('display', 'none');
                                            });
                                            $('#letpv_course').attr('value', idcourse);

                                        }
                                    });
                                });
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });

                });
                $('.abrirFechas').click(function () {
                    var params = $(this).attr('id');
                    var idcourse;
                    idcourse = params.substring(params.indexOf('(') + 1, params.indexOf(','));
                    var tpv = params.substring(params.indexOf(',') + 1, params.lastIndexOf(','));
                    $('#letpv_ventana-flotante').css('display', 'block');
                    $.ajax({
                        data: 'idcourse=' + idcourse,
                        url: 'eudemodaldates.php',
                        type: 'post',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#letpv_ventana-flotante').html(response);
                                $("button.letpv_cerrar").click(function close() {
                                    $('#letpv_ventana-flotante').css('display', 'none');
                                });
                                $('#letpv_course').attr('value', idcourse);

                                var course = $('input#letpv_course').val();
                                var modulo = $('.letpv_mod' + course + ' .c1 span').text();

                                var convoc = $('select#menuletpv_date').children().length;
                                for (var i = 1; i <= convoc; i++) {
                                    var opt = $('select#menuletpv_date option:nth-child(' + i + ')').text();
                                    if (modulo == opt) {
                                        $('select#menuletpv_date option:nth-child(' + i + ')').attr('selected',
                                                'selected');
                                    }
                                }
                                if (tpv == 1) {
                                    $('#letpv_amount').attr('value', '0');
                                } else if (tpv == 2) {
                                    $('form#fechas').attr('action', 'eudeprofile.php');
                                }
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }

                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            }
            modalAction();
            $('#menucategoryname').change(function () {
                var category = $('#menucategoryname').val();
                var numberCat = $('#menucategoryname').children().last().val();
                if (category !== 0) {
                    for (var i = 0; i <= numberCat; i++) {
                        if (i != category) {
                            $('.letpv_cat' + i).css('display', 'none');
                        } else {
                            $('.letpv_cat' + i).css('display', 'table-row');
                        }
                    }
                } else {
                    for (var j = 0; j < numberCat; j++) {
                        $('.letpv_cat' + j).css('display', 'table-row');
                    }
                }
                var catId = $('#menucategoryname').val();
                $.ajax({
                    data: 'catId=' + catId,
                    url: 'eudeprofilerequest.php?profilecat=' + catId,
                    type: 'post',
                    success: function (response, status, thrownerror) {
                        try {
                            window.console.log(response);
                            $('#letpv_student').empty();
                            $('#letpv_student').append(response.student);
                            $('#letpv_tablecontainer').empty();
                            $('#letpv_tablecontainer').append(response.table);
                            modalAction();
                        } catch (ex) {
                            window.console.log(ex.message);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    },
                    error: function (jqXHR, status, thrownerror) {
                        window.console.log(jqXHR.responseText);
                        window.console.log(status);
                        window.console.log(thrownerror);
                    }
                });
            });
            $('#letpv_student').change(function () {
                $('#letpv_student option:selected').each(function () {
                    var catId = $('#menucategoryname').val();
                    var studentId = $(this).val();
                    var spinner = "<div class='row' style='display: block;text-align: center;margin-top: 100px'>";
                    spinner += "<i class='fa fa-spinner fa-spin' style='font-size:120px;color:#6F1A3D'></i></div>";
                    $('#letpv_tablecontainer').append(spinner);
                    $.ajax({
                        data: 'catId=' + catId,
                        url: 'eudeprofilerequest.php?profilecat=' + catId + '&profilestudent=' + studentId,
                        type: 'post',
                        success: function (response, status, thrownerror) {
                            try {
                                $('#letpv_tablecontainer').empty();
                                $('#letpv_tablecontainer').append(response);
                                modalAction();
                            } catch (ex) {
                                window.console.log(ex.message);
                                window.console.log(status);
                                window.console.log(thrownerror);
                            }
                        },
                        error: function (jqXHR, status, thrownerror) {
                            window.console.log(jqXHR.responseText);
                            window.console.log(status);
                            window.console.log(thrownerror);
                        }
                    });
                });
            });
            var cat = $('#categoryselect').text();
            var options = $('#menucategoryname option').length;
            for (var i = 0; i <= options; i++) {
                if (cat == $('#menucategoryname option:nth-child(' + i + ')').text()) {
                    $('#menucategoryname option:nth-child(' + i + ')').attr('selected', 'selected');
                }
            }
            var category = $('#menucategoryname').val();
            var numberCat = $('#menucategoryname').children().last().val();
            if (category !== 0) {
                for (var i = 0; i <= numberCat; i++) {
                    if (i != category) {
                        $('.letpv_cat' + i).css('display', 'none');
                    } else {
                        $('.letpv_cat' + i).css('display', 'table-row');
                    }
                }
            } else {
                for (var j = 0; j < numberCat; j++) {
                    $('.letpv_cat' + j).css('display', 'table-row');
                }
            }
            $.ajax({
                data: 'catId=' + category,
                url: 'eudeprofilerequest.php?profilecat=' + category,
                type: 'post',
                success: function (response, status, thrownerror) {
                    try {
                        window.console.log(response);
                        $('#letpv_tablecontainer').empty();
                        $('#letpv_tablecontainer').append(response.table);
                        modalAction();
                    } catch (ex) {
                        window.console.log(ex.message);
                        window.console.log(status);
                        window.console.log(thrownerror);
                    }
                },
                error: function (jqXHR, status, thrownerror) {
                    window.console.log(jqXHR.responseText);
                    window.console.log(status);
                    window.console.log(thrownerror);
                }
            });
        },
        redirect: function () {
            $('.linkselect').change(function () {
                var course = ($(this).attr('course'));
                var notice = ($(this).attr('notice'));
                switch ($(this).val()) {
                    case '1':
                        window.location.href = '../../mod/forum/view.php?f=' + notice;
                        break;
                    case '2':
                        window.location.href = '../../mod/forum/index.php?id=' + course;
                        break;
                    case '3':
                        window.location.href = '../../mod/assign/index.php?id=' + course;
                        break;
                }
            });
        },
        menu: function () {
            $(document).ready(function () {
                var locat = window.location.href;
                var loc = locat.split("?");
                var path = window.location.pathname;
                if (path == "/local/eudecustom/eudeprofile.php" ||
                        path == "/local/eudecustom/eudegradesearch.php") {
                    $('.menulateral .icon-menu a:nth-child(2) li').addClass('selected');
                }
                if (path == "/local/eudecustom/eudeteachercontrolpanel.php" ||
                        path == "/grade/report/overview/index.php") {
                    $('.menulateral .icon-menu a:nth-child(3) li').addClass('selected');
                }
                for (var i = 2; i < 8; i++) {
                    var destino = $('.menulateral .icon-menu a:nth-child(' + i + ')').attr('href');
                    var des = destino.split("?");
                    if (loc[0] == des[0]) {
                        $('.menulateral .icon-menu a:nth-child(' + i + ') li').addClass('selected');
                    }
                }
            });
        },
        payment: function () {
            $(document).ready(function () {
                $('body').css('display', 'none');
                $('#id_submitbutton').click();
            });
        },
        dashboard: function () {
            $(document).ready(function () {
                $('button.dashboardbtntotal').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnincourse').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.incourse')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnfailed').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.failed')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnpassed').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.passed')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnconvalidated').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.convalidated')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnpending').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.pending')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnteachertotal').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnteacherincourse').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.activestudents')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnteacherpendingactivities').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.pendingactivities')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('button.dashboardbtnteacherpendingmessages').on( "click", function() {
                    $('div.dashboardcoursebox').show();
                    $('div.dashboardcoursebox').not($('div.dashboardcoursebox.pendingmessages')).hide();
                    $('button.dashboardbtn').removeClass('eudeactive');
                    $(this).addClass('eudeactive');
                });
                $('a.dashboardcourselink').on( "click", function(e) {
                    if($(this).closest("div.dashboardcoursebox.pending").length > 0) {
                        e.preventDefault();
                    }
                });
                $('a.nav-link').on( "click", function(e) {
                    $("li.nav-item").removeClass('active');
                    $(this).closest("li.nav-item").addClass('active');
                    var cat = $(this).attr('href');
                    $(cat).find('button.dashboardbtn.dashboardbtntotal').click();
                });
                'use strict';
                $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
                    var $target = $(e.target);
                    var $tabs = $target.closest('.nav-tabs-responsive');
                    var $current = $target.closest('li');
                    var $parent = $current.closest('li.dropdown');
                    $current = $parent.length > 0 ? $parent : $current;
                    var $next = $current.next();
                    var $prev = $current.prev();
                    var updateDropdownMenu = function($el, position){
                        $el
                            .find('.dropdown-menu')
                            .removeClass('pull-xs-left pull-xs-center pull-xs-right')
                            .addClass( 'pull-xs-' + position );
                    };
                    $tabs.find('>li').removeClass('next prev');
                    $prev.addClass('prev');
                    $next.addClass('next');
                    updateDropdownMenu( $prev, 'left' );
                    updateDropdownMenu( $current, 'center' );
                    updateDropdownMenu( $next, 'right' );
                });
            });
        }
    };
});
