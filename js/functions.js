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

    $('#myTable').DataTable( {
        "ajax": {
            "url": "includes/internalRequest.php",
            "data": {
                    "method":"getQuestions"
            },
            "method":"POST"
        },
        "columns": [
            { "data": "idPregunta" },
            { "data": "textoPregunta" },
            { "data": "estadoPregunta" },
            { "data": "fechaRecibida" },
            { "data": "textoRespuesta" },
            { "data": "fechaRespuesta" },
            { "data": "idUsuario" },
            { "data": "idItem" },
            { "data": "demoraRtaSeg" },
            { "data": "cantPreguntasUsuario" }
        ]
    } );
    
      

});