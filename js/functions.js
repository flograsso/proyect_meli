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
            table-listaPreguntas  ;
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
              
        }
    });

});

function addRow(table,content)
{
    for (x in myObj)
    {
        $("#"+table + " tbody").append("<tr");
        $("#"+table + " tbody").append("<td>"+myObj[x]+"</td>");
        $("#"+table + " tbody").append("</tr");
        
    }
}