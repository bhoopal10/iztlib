var chart_relTarget = "";
$(function(){
    var widget_list = $('.widget-list').length;
    if(widget_list) getWidget();
    var w_typr = $('#widget_type').val();
    if(w_typr == 1){
        $('#multiple-widget').addClass('hide');
    }else{
        $('#multiple-widget').removeClass('hide');
    }
    $('#widget-btn').click(function(){
        $('.widget-content').toggleClass('hide');
            $('#search-bar').collapse('toggle');
    });
    var having_filter = $("select[name=widget_having_name]");
    var repid = $("#save-widget").attr('data-repid');
    if(having_filter.length){
        var tbl_header = $("#frm1 table tr:first th");
        var options = "";
        $.each(tbl_header,function(k,v){
            var txt = $(v).text();
            options += '<option value="'+txt+'">'+txt+'</option>';
        });
        having_filter.append(options);
        if(repid){
            $('#widget_x_axis,#widget_y_axis,#chart_fields').html(options);
        }
        $("select[name=widget_order_by]").append(options);
        var selected = having_filter.attr('data-selected');
        var order_selected = $("select[name=widget_order_by]").attr('data-selected');
        var selected_op = $("select[name=widget_having_operator]").attr('data-selected');
        having_filter.val(selected);
        $("select[name=widget_having_operator]").val(selected_op);
        $("select[name=widget_order_by]").val(order_selected);
    }
    $('#chart-modal').on('show.bs.modal',function(e){
        var fragment = document.createDocumentFragment();
        var btn = $(e.relatedTarget).parent().parent().find("div[id^='chartdiv']")[0];
        chart_relTarget = $(btn).parent();
        fragment.appendChild(btn);
        var model = $(e.currentTarget).find('.modal-body div');
        model.html(fragment);
        model.find("div[id^='chartdiv']").css({width:"100%",height:"300px"});
    });
    $('#chart-modal').on('hide.bs.modal',function(e){
        var fragment = document.createDocumentFragment();
        var btn = $(e.currentTarget).find("div[id^='chartdiv']")[0];
        fragment.appendChild(btn);
        chart_relTarget.html(fragment);
        chart_relTarget.find("div[id^='chartdiv']").css({width:"242px",height:"123px"});
    });
    $('#filterbtn').click(function(){
        $('.widget-content').addClass('hide');
    });
    $('#search-bar').on('hide.bs.collapse',function(){
        $('.widget-content').addClass('hide');
    });
    $('#widget_type').change(function(){
        var curr = $(this).val();
        if(curr == 1){
            $('#single-widget').removeClass('hide');
            $('#multiple-widget,#chart-widget-div').addClass('hide');
        }else if(curr == 2){
            $('#single-widget,#multiple-widget').removeClass('hide');
            $('#chart-widget-div').addClass('hide');
        }else {
            $('#single-widget,#multiple-widget').addClass('hide');
            $('#chart-widget-div').removeClass('hide');
        }
    });
    $('#widget_chart_type').change(function(){
        if($(this).val() == 'pie'){
            $('.widget_x_axis,.widget_y_axis').addClass('hide');
        }else{
            $('.widget_x_axis,.widget_y_axis').removeClass('hide');
        }
    });

    $('#save-widget').click(function(e){
        e.preventDefault();
        saveWidget(function(){

        });
    });

});

function saveWidget(cb){
    var url = $('#save-widget').attr('data-url');
    var repid = $('#save-widget').attr('data-repid');
    var all_data = $("#widgetmodal input,.widget-content input,.widget-content select,#qual_field");
    var required_fields = ["widget_label","widget_type","widget_field","widget_operator","widget_name","widget_label"];
    if($('#widget_type').val() == '2'){
        required_fields = ["widget_label","widget_type","widget_field","widget_operator","multi_delimit","multi_widget_label","multi_widget_field","multi_widget_operator","widget_name","widget_label"];
    }
    var alert_msg ="";
    var flag = false;
    var final_data = new Array();
    $.each(all_data,function(k,v){
       var name = $(v).attr('name');
       var value = $(v).val();
        final_data.push({name:name,value:value});
    });
    if(repid) final_data.push({name:"repid",value:repid});
    // $.each(all_data,function(k,v){
    //     if(!v[0]){
    //         alert_msg += "The "+v[1]+" is required!.<br>";
    //         flag = true;
    //     }
    // });
    // if(flag){
    //     customAlert(alert_msg,function(){});
    //     return false;
    // }
    $.ajax({
        type:'post',
        url:url,
        data:final_data,
        beforeSend:function(){
            $('#save-widget').prop('disabled',true);
        },
        complete: function(){
            $('#save-widget').prop('disabled',false);
        },
        success :function(res){
            $('#widgetmodal').modal('hide');
            $('#search-bar').collapse('hide');
            cb();
        }
    });
}

function getWidget(){
    var tbl = $(".widget-list").attr('data-table');
    var qual = $(".widget-list").attr('data-qual');
    if(!qual) qual = '1=1';
    $.ajax({
        type:'post',
        url:'lib/widgets/get-widget.php',
        data:{tbl:tbl,qual:qual},
        beforeSend:function(){
           $('.widget-list').html('<div class="fa-5x text-center"><i class="fa fa-spin fa-spinner"></i></div>');
        },
        complete: function(){
        },
        success :function(res){
            $('.widget-list').html(res.success);

        }
    })
}