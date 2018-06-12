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
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "includes/internalRequest.php",
                data: {
                "method":"getQuestions"
            }
        }
    } );
    
      

});