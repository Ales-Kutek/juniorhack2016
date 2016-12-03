//nastevení češtiny pro moment.js
moment.locale("cs");

var _data = null;
var _id = null;
        
$(function () {
    
        $("._room").click(function() {
            _id = $(this).attr("data-id");
            
            _data = $.param({
                do: "getChart",
            });
            
            $.ajax({
                type: "GET",
                data: _data,
                dataType: "json",
                url: window.location.href + "/homepage/default/" + _id + "/",
                success: function(data) {
                    console.log(data.length);
                    
                    var options;
                    var ar = new Array();
                    var chart = {};
                    var _date = [];
                    
                    for (var x = 0; x < data.length; x++) {
                        console.log("Lfdsfg");
                        ar = new Array();
                        options = {
                        chart: {
                                renderTo: 'main-chart',
                                defaultSeriesType: 'spline'
                            },
                         series: [],
                             xAxis: {
                                type: 'datetime' //ensures that xAxis is treated as datetime values
                            }
                        }
                        
                        for (var y = 0; y < data[x]["category"].length; y++) {
                            _date = data[x]["category"][y];
                            
                            ar.push([_date, data[x]["data"][y]]);
                        }
                        
                        console.log(ar);
                        
                        options.series = [
                            {
                                data: ar
                            }
                        ];
                        
                        chart[x] = new Highcharts.Chart(options);
                    }
            }
        });
    });
});

/**
 * slug
 */
var slugify = function (str) {
    var charlist = [
        ['Á', 'A'], ['Ä', 'A'], ['Č', 'C'], ['Ç', 'C'], ['Ď', 'D'], ['É', 'E'], ['Ě', 'E'],
        ['Ë', 'E'], ['Í', 'I'], ['Ň', 'N'], ['Ó', 'O'], ['Ö', 'O'], ['Ř', 'R'], ['Š', 'S'],
        ['Ť', 'T'], ['Ú', 'U'], ['Ů', 'U'], ['Ü', 'U'], ['Ý', 'Y'], ['Ž', 'Z'], ['á', 'a'],
        ['ä', 'a'], ['č', 'c'], ['ç', 'c'], ['ď', 'd'], ['é', 'e'], ['ě', 'e'], ['ë', 'e'],
        ['í', 'i'], ['ň', 'n'], ['ó', 'o'], ['ö', 'o'], ['ř', 'r'], ['š', 's'], ['ť', 't'],
        ['ú', 'u'], ['ů', 'u'], ['ü', 'u'], ['ý', 'y'], ['ž', 'z']
    ];
    for (var i in charlist) {
        var re = new RegExp(charlist[i][0], 'g');
        str = str.replace(re, charlist[i][1]);
    }

    str = str.replace(/[^a-z0-9]/ig, '-');
    str = str.replace(/\-+/g, '-');

    var newStr = "";

    for (i = 0; i < str.length; i++) {
        if (str[i].match(/[a-z]/i)) {
            newStr += str[i];
        }
    }

    return str.toLowerCase();
}

jQuery(document).ready(function ($) {
    //nasazení input mask pluginu
    $(":input").inputmask();

    $('input[value="Odstranit"]').parent().addClass('inputHelpic');

    $(" input[data-input='placeit'] ,  input[data-input='placeittop'] ").label_better({
        position: "right",
        animationTime: 300,
        easing: "ease-in-out",
        offset: -50,
        hidePlaceholderOnFocus: true
    });
//    $(" input[data-input='placeittop'] ").label_better({
//        position: "top",
//        animationTime: 300,
//        easing: "ease-in-out",
//        offset: 0,
//        hidePlaceholderOnFocus: true
//    });

    $('.lb_wrap').prevAll("label").hide();

    //nasazení hezkých checkboxů
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });

    /****** datapicker *******/
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    $(".actualdate").each(function () {
        if ($(this).val() == "" && $(this).val() !== undefined && $(this).val() !== null) {
            $(this).val(dd + ". " + mm + ". " + yyyy);
        }
    });

    $(".datepicker").closest("div").each(function () {
        if (!$(this).children("input").hasClass("datepicker-input")) {
            $(this).append('<div class="datepicker_inline"></div>');
            $(this).children("input").hide();
        }
    });

    $(".datepicker_inline").each(function () {
        var value = $(this).parent("div").children("input").val();

        $(this).data("date", value);
    });

    var datepicker_settings = {
        format: 'd. m. yyyy',
        language: "cs",
        weekStart: 1
    };

    var datepicker = $('.datepicker_inline').datepicker(
            datepicker_settings
            );

    datepicker.on("changeDate", function () {
        $(this).parent("div").children("input").val($(this).datepicker('getFormattedDate')).trigger("change");
        $(this).parent("div").children("input").attr("value", $(this).datepicker('getFormattedDate'));
    });

    datepicker_settings["autoclose"] = true;
    $('.datepicker-input').datepicker(datepicker_settings);

    /***** datepicker END ******/

    //ublaboo datagrid - odkaz přes celou řádku kromě action sloupce
    $("table").on("click", ".grid-href td", function () {
        if ($(this).closest(".noDetail").length == 0) {
            var main_href = $(this).children("a").attr("href");

            var href = $(this).closest("tr").data("href");

            var classes = $(this).attr("class").split(" ");

            if (classes[0] != "col-action") {
                if (main_href !== null && main_href !== undefined && main_href != "") {
                    window.location.href = main_href;
                } else {
                    window.location.href = href;
                }
            }
        }
    });

    $("#newVisit").click(function () {
        event.preventDefault();
        $('#newVisitModal').modal({});
    });
    $("#newComa").click(function () {
        event.preventDefault();
        $('#newComaModal').modal({});
    });

    $(".closeBtn").click(function () {
        event.preventDefault();
        $('#newVisitModal').modal('hide');
        $('#newComaModal').modal('hide');
    });

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.sticky').addClass("open");
            $('.topButton').addClass("stickyToo");
            $('.sidebar-toggle').addClass("stickyToggle");
        } else {
            $('.sticky').removeClass("open");
            $('.topButton').removeClass("stickyToo");
            $('.sidebar-toggle').removeClass("stickyToggle");
        }

        if ($(window).height() < 900) {

        } else {
            if ($(window).scrollTop() > 50) {
                $('.main-sidebar').addClass("sticked");
            } else {
                $('.main-sidebar').removeClass("sticked");
            }
        }
    });


    if ($(".sidebar-mini").length > 0) {
        $('*[data-cookie="' + $.cookie("sidebar_type") + '"]').addClass('active');
        $(".sidebar-toggle").click(function (e) {
            $(".sidebar-toggle").removeClass('active');
            $(this).addClass('active');
            $.cookie("sidebar_type", $(this).data('cookie'));
            $(".sidebar-mini").removeClass('sidebar-open').removeClass('sidebar-collapse');
            $(".sidebar-mini").addClass($(this).data('cookie'));
        });
    }

    /** sticky heading **/
    var _sticky_heading = $("*[data-heading='true']");
    var _sticky_fields = {};

    var __string = "";

    _sticky_heading.each(function () {
        var _value = $(this).val();

        _sticky_fields[$(this).attr("name")] = $(this).val();
        __string += " " + _value;
    });

    if (__string != "")
        $(".sticky_heading").html(" -" + __string);

    _sticky_heading.each(function () {
        $(this).keyup(function () {
            _sticky_fields[$(this).attr("name")] = $(this).val();

            var string = "";

            $.each(_sticky_fields, function (i, v) {
                string += " " + v;
            });

            $(".sticky_heading").html(" -" + string);
        });
    });
    

    // if menu is open then true, if closed then false
    // we start with false
    var open = false;
    // just a function to print out message
    function isOpen() {
        if (open)
            $('.formNew.formNew--client select').prevAll('label').addClass('openlabe'),
                    $('.formNew.formNew--client select').prevAll('label').removeClass('closelabe');
        else
            $('.formNew.formNew--client select').prevAll('label').addClass('closelabe'),
                    $('.formNew.formNew--client select').prevAll('label').removeClass('openlabe');
    }
    // on each click toggle the "open" variable
    $(".formNew.formNew--client select").on("click", function () {
        open = !open;
        console.log(isOpen());
    });
    // on each blur toggle the "open" variable
    // fire only if menu is already in "open" state
    $("#frm-new-region").on("blur", function () {
        if (open) {
            open = !open;
            console.log(isOpen());
        }
    });
    // on ESC key toggle the "open" variable only if menu is in "open" state
    $(document).keyup(function (e) {
        if (e.keyCode == 27) {
            if (open) {
                open = !open;
                console.log(isOpen());
            }
        }
    });
    
    $('input').keypress(function (e) {
        if (e.which == 13) {
            var form = $(this).closest("form");
            
            form.find(':submit').each(function() {
                if ($(this).val() == "Uložit a zůstat") {
                    $(this).click();
                }
            });
            
          return false;
        }
    });
    
    var submittedByButton = false;
    
    $(window).on('beforeunload', function(e){
        var leave = false;
        $("form").each(function() {
            if ($(this).attr("data-catchunload") == "true") {

                if($(this).serialize()!= $(this).attr('data-serialize')) {
                    if (!leave) {
                        leave = true;
                    }
                } else {
                    e = null; // i.e; if form state change show warning box, else don't show it.
                }
        }
        });
        
        if (leave && submittedByButton == false) {
            return "Opravdu chcete opustit tuto stránku?";
        }
    });
    
    $("form").each(function() {
        var _form = $(this);
        
        $(this).find("input[type='submit']").each(function() {
            if ($(this).val() == "Uložit" || $(this).val() == "Odeslat") {
                _form.attr("data-catchunload", "true");
            }
        });
        
        //give the little time to load all javascript shit :)
        setTimeout(function() {
            _form.attr('data-serialize', _form.serialize());
        }, 666);
        
        $(this).submit(function() {
            submittedByButton = true;
        });
    });
});
