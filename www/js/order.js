var hireDateDisabled = null;

var unsaved = false;
$(":input").change(function () {
    unsaved = true;
});
function unloadPage() {
    if (unsaved) {
        return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
    }
}
window.onbeforeunload = unloadPage;
$('.form-actions .btn.btn-default').click(function () {
    unsaved = false;
});

var verifyMachineDate = function(hire_begin, hire_end, machine_id) {

    if (hire_begin != "" && hire_end != "" && machine_id != "") {
        var data = {
            from: hire_begin,
            to: hire_end,
            machine_id: machine_id,
            "do": "machineDate"
        };

        data = $.param(data);

        $.ajax({
            url: window.location.href,
            dataType: "json",
            data: data,
            method: "GET",
            success: function(data) {
                if (data["snippets"]["snippet--flashMessage"] != "") {
                    $("#snippet--flashMessage").html(data["snippets"]["snippet--flashMessage"]);
                }
                
            },
            error: function(xhr) {
                console.log("deleted");
                $("#snippet--flashMessage").html("");
            }
        });
    }
};

var getHireDates = function(machine_id) {

    if (machine_id != "") {
        var data = {
            machine_id: machine_id,
            "do": "getHireDates"
        };

        data = $.param(data);

        $.ajax({
            url: "/commission",
            dataType: "json",
            data: data,
            method: "GET",
            success: function(data) {
                hireDateDisabled = data;
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
};

var getMachinePrice = function(hire_begin, hire_end, machine_id) {
    if (hire_begin != "" && hire_end != "" && machine_id != "") {
        var data = {
            from: hire_begin,
            to: hire_end,
            machine_id: machine_id,
            "do": "machineDefaultPrice"
        };

        data = $.param(data);

        $.ajax({
            url: "/order/new",
            dataType: "json",
            data: data,
            method: "GET",
            success: function(data) {
                if (data["result"] != false) {
                    $(".machine_price").html("Cena: " + data["result"]);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
};

var getGearPrice = function(hire_begin, hire_end, gear_id, selector) {
    if (hire_begin != "" && hire_end != "") {
        var data = {
            from: hire_begin,
            to: hire_end,
            gear_id: gear_id,
            "do": "gearDefaultPrice"
        };

        data = $.param(data);

        $.ajax({
            url: "/order/new",
            dataType: "json",
            data: data,
            method: "GET",
            success: function(data) {
                if (data["result"] != false) {
                    selector.parent("div").children("div").html("Cena: " + data["result"]);
                }
            },
            error: function(xhr) {
            }
        });
    }
};

var countDiscount = function(price, discount) {
    if (price != "" && discount != "") {
        var result = price - discount;

        result = Math.ceil(result / 10) * 10;

        if ($(".discount_box").length != 0) {
            $(".discount_box").html(result);
        } else {
            $("input[name='discount']").closest("div").append('<span class="discount_box">' + result + '</span>');
        }
    }
};

$(document).ready(function() {
    $("input[name='hire_begin'], input[name='hire_end']").daterangepicker({
        locale: {
            format: 'D. M. YYYY'
        },
        isInvalidDate: function(date) {
            if (hireDateDisabled !== null) {
                var start = null;
                var end = null;
                var start_clone = null;
                
                var range = [];
                
                if ($("input[name='hire_begin']").data('daterangepicker').startDate !== null) {
                    start = $("input[name='hire_begin']").data('daterangepicker').startDate.format("D. M. YYYY");
                    var start_clone = start;
                    start = moment(start, "D. M. YYYY");
                }
                
                if ($("input[name='hire_begin']").data('daterangepicker').endDate !== null) {
                    end = $("input[name='hire_begin']").data('daterangepicker').endDate;
                }
                
                if (start !== null && end !== null) {
                    while (start.format("D. M. YYYY") != end.format("D. M. YYYY")) {
                        range.push(start.format("D. M. YYYY"));
                        start.add("1", "days");
                    }
                }
                
                var errored = false;
                
                for (var y = 0; y < range.length; y++) {
                    for (var o = 0; o < hireDateDisabled.length; o++) {
                        if (range[y] == hireDateDisabled[o]) {
                            if (!errored) {
                                $("input[name='hire_begin']").data('daterangepicker').setStartDate(start_clone);
                                $("input[name='hire_begin']").data('daterangepicker').setEndDate(start_clone);

                                var data = {
                                    "do": "badDateRange"
                                };

                                $.ajax({
                                    url: window.location.href,
                                    dataType: "json",
                                    data: data,
                                    method: "GET",
                                    success: function(data) {
                                        $("#snippet--flashMessage").append(data.snippets["snippet--flashMessage"]);
                                    },
                                    error: function(xhr) {
                                    }
                                });
                                
                                errored = true;
                            }
                        }
                    }
                }
                
                if (!errored) {
                    $("#snippet--flashMessage").html("");
                }
                
                for (var x = 0; x < hireDateDisabled.length; x++) {
                    if (date.format("D. M. YYYY") == hireDateDisabled[x]) {
                        return true;
                    }
                }
            }

            return false;
        },
        autoUpdateInput: false,
        autoApply: true
    });

    $("input[name='hire_begin'], input[name='hire_end']").on('apply.daterangepicker', function(ev, picker) {
        $("input[name='hire_begin']").val(picker.startDate.format('D. M. YYYY'));
        $("input[name='hire_end']").val(picker.endDate.format('D. M. YYYY'));
    });

    $("select[name='machine']").change(function() {
        var hire_begin = $("input[name='hire_begin']").val();
        var hire_end = $("input[name='hire_end']").val();
        var machine_id = $("select[name='machine']").val();

        verifyMachineDate(hire_begin, hire_end, machine_id); 
        getMachinePrice(hire_begin, hire_end, machine_id);
        getHireDates(machine_id);
    });

    $("select[name='machine']").trigger("change");

    $("form").on("change", "input[name='hire_begin']", function() {
        var hire_begin = $("input[name='hire_begin']").val();
        var hire_end = $("input[name='hire_end']").val();
        var machine_id = $("select[name='machine']").val();

        verifyMachineDate(hire_begin, hire_end, machine_id); 
        getMachinePrice(hire_begin, hire_end, machine_id);
    });

    $("form").on("change", "input[name='hire_end']", function() {
        var hire_begin = $("input[name='hire_begin']").val();
        var hire_end = $("input[name='hire_end']").val();
        var machine_id = $("select[name='machine']").val();

        verifyMachineDate(hire_begin, hire_end, machine_id); 
        getMachinePrice(hire_begin, hire_end, machine_id);
    });

    $(".select_gear").change(function() {
        var hire_begin = $("input[name='hire_begin']").val();
        var hire_end = $("input[name='hire_end']").val();
        var gear_id = $(this).val();

        getGearPrice(hire_begin, hire_end, gear_id, $(this));
    });

    $(".select_gear").trigger("change");

    countDiscount($("input[name='daily_hire_price']").val(), $("input[name='discount']").val());

    $("input[name='daily_hire_price']").keyup(function() {
        var price = $("input[name='daily_hire_price']").val();
        var discount = $("input[name='discount']").val();

        countDiscount(price, discount);
    });

    $("input[name='discount']").keyup(function() {
        var price = $("input[name='daily_hire_price']").val();
        var discount = $("input[name='discount']").val();

        countDiscount(price, discount);
    });

    $("select[name='payment_method']").change(function() {
        if ($(this).val() != "") {

            var data = {
                payment_id: $(this).val(),
                "do": "paymentMethod"
            };

            data = $.param(data);

            $.ajax({
                url: "/order/new",
                dataType: "json",
                data: data,
                method: "GET",
                success: function(data) {
                    $("textarea[name='payment_method_text']").val(data);
                },
                error: function(xhr) {
                }
            });
        }
    });

    $("select[name='client']").change(function() {

        if ($(this).val() != "") {
            var data = {
                client_id: $(this).val(),
                "do": "clientMaturity"
            };

            data = $.param(data);

            $.ajax({
                url: "/order/new",
                dataType: "json",
                data: data,
                method: "GET",
                success: function(data) {
                    $("input[name='maturity']").val(data);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
});