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
            $("#table-listaPreguntas").DataTable( {
                "displayLength": 25,
                "order": [
                    [2, 'asc']
                ],
                "columnDefs": [{
                    "visible": false,
                    "targets": 2
                }],
            } );
            
        }
    });
    

});

function addRow(_table,_content)
{
    var str="<tr>";
     
    $("#"+_table + " tbody").append("<tr>");
    for (x in _content)
    {  
        str=str+"<td>"+_content[x]+"</td>"
    }
    str=str+"</tr>";

    $("#"+_table + " tbody").append(str);
}

