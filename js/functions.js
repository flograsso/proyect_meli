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
                    'method':'getQuestionsUnanswered',
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
                "displayLength": 10,
                "order": [
                    [3, 'desc']
                ],

            } );
            
        }
    });
    $("#vista-preguntas-fecha").change(function() {
        $.ajax({
            url: 'includes/internalRequest.php',
            type: 'post',
            datatype: 'json',
            data: 	{
                        'method':'getQuestionsDelay',
                        'date':$("#vista-preguntas-fecha").val(),
                    },
            success:  function (response) {
                var time = []
                var delay = [];
                myObj = JSON.parse(response);	
                for (x in myObj) {
                    time.push(myObj[x].fechaRespuesta);
                    delay.push(Math.round(myObj[x].demoraRtaSeg / 60));
                }	

                var chartdata = 
				{
					type: 'line',
					data: 
					{
						labels:time,
						datasets: 
							[{
							data: rssi,
							label: "rssi+100",
							backgroundColor: "rgba(241, 0, 0, 1)",
							borderColor: "rgba(241, 0, 0, 1)",
							fill:false,
							
						}],
					},
					options: {
						scales: {
							xAxes: [{
								type: 'time',
								
								
				
							}]	

						}
					}
				};
            }

        });
    });


    

});

function addRow(_table,_content)
{
    var str="<tr>";
     
    for (x in _content)
    {  
        str=str+"<td>"+_content[x]+"</td>"
    }
    str=str+"</tr>";

    $("#"+_table + " tbody").append(str);
}

