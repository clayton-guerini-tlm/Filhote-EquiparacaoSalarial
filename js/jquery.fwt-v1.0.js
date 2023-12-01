/**
 * Fwt extension
 * Renan Abreu renanhabreu@gmail.com
 *
 * GPL licenses:
 * http://www.gnu.org/licenses/gpl-2.0.html
**/
(function(jQ){

    var defaultOptAlerta = {
        message:"Houve uma falha ao realizar esta operacao",
        ui:"ui-state-highlight",
        icone:"ui-icon-alert"
    };

    var defaultOptFormulario = {
        title:"Formulario de usuario",
        btn1:'Salvar',
        btn2:'Cancelar',
        closeInSuccess:true,
        success:function(){},
        failure:function(){},
        posClickBtn1:function(){},
        posClickBtn2:function(){}
    };


    jQ.fwtAlerta = function(options){
        jQ.fn.extend(defaultOptAlerta, options);

        var div  = '<div id="alert">';
        div += '    <div class="'+defaultOptAlerta.ui+'">';
        div += '        <p style="text-align:left">';
        div += '            <span class="ui-icon '+defaultOptAlerta.icone+'" style="float:left;margin-right:.3em">';
        div += '            </span>';
        div += '            '+defaultOptAlerta.message;
        div += '        </p>';
        div += '    </div>';
        div += '</div>';

        jQ('body').prepend(div);
        jQ('#alert').dialog({
            title:"Alerta",
            autoOpen:true,
            modal:true,
            buttons:[
            {
                text:"OK",
                click:function(){
                    jQ("#alert").dialog("destroy");
                    $("#alert").remove();
                }
            }
            ]
        });
        jQ('#alert').dialog("open");

    }

    jQ.fn.fwtValidar = function(type,value){
        
        for(var key in validadores){
            if(key == type){
                var exp = validadores[key];
                return exp.test(value);
            }
        }

        return false;

    };

    jQ.fn.fwtFormulario = function(options){

        jQ.fn.extend(defaultOptFormulario,options);

        this.each(function(){
            var window = jQ(this);
            window.dialog({
                autoOpen:true,
                modal:true,
                title:defaultOptFormulario.title,
                buttons:[
                {
                    text:defaultOptFormulario.btn1,
                    click:function(btn){

                        var btnSave     = jQ(":button:contains('"+defaultOptFormulario.btn1+"')");
                        var form        = jQ(this).children('form');
                        var params      = new Array();
                        var data        = "";

                        if(form.length > 0){

                            btnSave.attr("disabled","disabled").addClass('ui-state-disabled');
                            jQ.each(form[0].elements,function(index,field){
                                params.push('"'+jQ(field).attr('name')+'"' + ':"'+jQ(field).val()+'"');
                            });
                            data = '{'+params.join(',')+'}';

                            jQ.getJSON(jQ(form[0]).attr('action'), jQ.parseJSON(data),function(response){
                                if(response.success){
                                    jQ.fwtAlerta({
                                        message: response.message
                                    });
                                    window.dialog('destroy');
                                    defaultOptFormulario.success();
                                }else{
                                    jQ.fwtAlerta({
                                        message: response.message,
                                        ui:'ui-state-error'
                                    });
                                    defaultOptFormulario.failure();
                                }
                            })
                            .error(function(error){
                                jQ.fwtAlerta({
                                    message: error.statusText+" n."+error.status,
                                    ui:'ui-state-error'
                                });
                                defaultOptFormulario.failure();
                            });
                            btnSave.removeAttr("disabled").removeClass('ui-state-disabled');
                        }

                        defaultOptFormulario.posClickBtn1();
                    }
                },
                {
                    text:'Limpar',
                    click:function(){
                        var form = jQ(this).children('form');
                        if(form.length > 0){
                            jQ.each(form[0].elements,function(key,field){
                                jQ(field).val('');
                            });
                        }

                        defaultOptFormulario.posClickBtn2();
                    }
                }
                ]
            });
        });

    }

    jQ.fn.fwtGrid = function(options){
        var defaultOption = {
            url:"retorno_teste.json",
            columns:[
            {
                name:'id',
                index:'id',
                label:'Id',
                width:55
            }
            ]
        };

        jQ.fn.extend(defaultOption,options);

        /*this.each(function(){*/
        jQ(this).jqGrid({
            url:defaultOption.url,
            datatype:"json",
            mtype:'POST',
            autowidth:true,
            height:"auto",
            jsonReader:{
                root:"items",
                page: "currpage",
                total: "totalpages",
                records: "totalrecords",
                repeatitems: true,
                cell: "registro",
                id: "0"
            },
            colModel : defaultOption.columns
        }).navGrid(".app-grid-pager");
    //  });

    }

    /****
     *  Fields
     */
    var validadores = {
        "ip":/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
        ,
        "email":/^[\w\-\+\&\*]+(?:\.[\w\-\_\+\&\*]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/
        ,
        "date":/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/
        ,
        "number":/^.*[0-9]$/
        ,
        "alfa":/^.*[a-zA-Z]$/
        ,
        "noempty":/^.+$/
    };

    var defaultOptField = {
        name:'field',
        id:null,
        cls:null,
        label:'Field',
        style:'float:left;width:100%;padding:2px;text-transform: uppercase;'
    }

    jQ.fwtExtendValidadores = function(options){
        jQ.extend(validadores,options);
    };

    jQ.fn.fwtFieldText = function(options){
        var cfg = {
            type:'text',
            value:'',
            validator:''
        }
        jQ.extend(cfg,defaultOptField,options);

        var input  = '<label style="padding:4px;text-align:left;float:left;width:97%;">'+cfg.label;
        input += '<input value="'+cfg.value+'" name="'+cfg.name+'" validator="'+cfg.validator+'" style="'+cfg.style+'" />';
        input += '</label>';
        return jQ(this).append(input).children(":last").children('input');
    }

    jQ.fn.fwtFieldSelect = function(options){
        var cfg = {
            url:false,
            data:{},
            options:{},
            value:'',
            validator:''
        }
        var $this = this;
        var element = null;

        jQ.extend(cfg,defaultOptField,options);

        var buildField = function(cfg){
            var input  = '<label style="padding:4px;text-align:left;float:left;width:97%;">'+cfg.label;
            input += '<select name="'+cfg.name+'" validator="'+cfg.validator+'" style="'+cfg.style+'">';
            for(var key in cfg.options){
                var selected = '';
                if(cfg.options[key] == cfg.value){
                    selected = 'selected="selected"';
                }
                input += '<option value="'+cfg.options[key]+'" '+selected+'>'+key+'</option>';
            }
            input += '</select>';
            input += '</label>';
            element = jQ($this).append(input).children(":last").children('select');
        }

        if(cfg.url){
            jQ.getJSON(cfg.url,cfg.data ,function(response){
                jQ.extend(cfg.options,response);
                buildField(cfg);
            })
            .error(function(error){
                jQ.fwtAlerta({
                    message: error.statusText+" n."+error.status,
                    ui:'ui-state-error'
                });
            });
        }else{

            buildField(cfg);
        }

        return element
    }

    /**
     * Field Events
     */

    var defaultOptFieldLoad = {
        url:'',
        params:''
    }

    jQ.fn.fwtFieldLoadValue = function(options){
        var cfg = {};
        var $this = this;

        jQ.fn.extend(cfg,defaultOptFieldLoad,options);

        jQ.getJSON(cfg.url,cfg.data ,function(response){
            $this.val(response.value);
        })
        .error(function(error){
            jQ.fwtAlerta({
                message: error.statusText+" n."+error.status,
                ui:'ui-state-error'
            });
        });
    }

})(jQuery);