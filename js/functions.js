$(document).ready(function(){
    var arrayPreguntas = [];

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
            for (x in myObj) { 
                arrayPreguntas[x].push(myObj[x].idPregunta);
                arrayPreguntas[x].push(myObj[x].textoPregunta);
            }     
            console.log(arrayPreguntas);    
        }
    });


    /*
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
*/
    
     
    $(document).ready(function() {
        $('#example1').DataTable( {
            data: arrayPreguntas,
            columns: [
                { title: "idPregunta" },
                { title: "textoPregunta" },
                /*
                { title: "estadoPregunta" },
                { title: "fechaRecibida" },
                { title: "textoRespuesta" },
                { title: "fechaRespuesta" },
                { title: "idUsuario" },
                { title: "idItem" },
                { title: "demoraRtaSeg" },
                { title: "cantPreguntasUsuario" }
                */
            ]
        } );
    } );
    
      

});