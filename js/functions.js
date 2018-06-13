$(document).ready(function(){


    $("#enviarQuery").click(function() 
    {
        $.ajax({
            url: 'includes/internalRequest.php',
            type: 'post',
            datatype: 'json',
            data: 	{
                        'method':'executeQuery',
                        'query':$("#consulta").val()
                    },
            success:  function (response) {
                $("#resultado").val(response);                 
            }
        });
    });

    $.ajax({
        url: 'includes/internalRequest.php',
        type: 'post',
        datatype: 'json',
        data: 	{
                    'method':'getQuestions',
                },
        success:  function (response) {
            myObj=JSON.parse(response) ;  
            var data = []
            for (x in myObj) { 
               data=[];
               data.push(myObj[x].textoPregunta);
               data.push(myObj[x].textoRespuesta);
               data.push(Math.round(myObj[x].demoraRtaSeg / 60));
               data.push(myObj[x].fechaRecibida);
               data.push(myObj[x].fechaRespuesta);
               addRow("table-listaPreguntas",data); 
            }
            $('#table-listaPreguntas').DataTable();     
            var table = $('#table-listaPreguntas').DataTable({
                "columnDefs": [{
                    "visible": false,
                    "targets": 2
                }],
                "order": [
                    [2, 'asc']
                ],
                "displayLength": 25,
                "drawCallback": function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var last = null;
                    api.column(2, {
                        page: 'current'
                    }).data().each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                            last = group;
                        }
                    });
                }
            });
            // Order by the grouping
            /*
            $('#table-listaPreguntas tbody').on('click', 'tr.group', function() {
                var currentOrder = table.order()[0];
                if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                    table.order([2, 'desc']).draw();
                } else {
                    table.order([2, 'asc']).draw();
                }
            });
        }
    });

});

function addRow(_table,_content)
{
    $("#"+_table + " tbody").append("<tr>");
    for (x in _content)
    { 
        $("#"+_table + " tbody").append("<td>"+_content[x]+"</td>"); 
    }
    $("#"+_table + " tbody").append("</tr>");
}

