<?php 
// se ingresa una palabra por vez. No se recolecta ni hay process.php

$formValues = array(
    'source' => ($template['formValues']['source'] ?: ''),
    'date' => ($template['formValues']['date'] ?: ''),
    'word_tag' => ($template['formValues']['tag'] ?: ''),
    'priority' => ($template['formValues']['priority'] ?: null),
);

?>


<style>

form textarea {
    max-width: 470px; 
    width: 100%; 
    height: 100px;
    vertical-align:top;
    font-size:1.3em;
}


form .row {
    display: flex;
    flex-direction: column;
}

@media screen and (min-width: 500px) {
    form .row {
        flex-direction: row;
    }
}


form .row p {
    margin-right: 1em;
}

</style>


<hr> 

<form id="form_input" action="" method="post">

    <div class="row">
        <p><label for="word_source">Origen:</label>
        <input type="text"  name="new_source" id="word_source" required value="<?php echo $formValues['source'];?>" >
        </p>


        <p>
        <label for="word_date">Fecha particular:</label>
        <input type="date"  name="new_date" id="word_date" value="<?php echo $formValues['date'];?>">
        </p>
    </div>

    <div class="row">
        <p>
        <label for="word_tag">Etiqueta:</label>
        <select name="new_word_tag" id="word_tag" required>
            <?php foreach($template['tags'] as $tag): 
                // saltear la primera que sería la de "estudio" (no tiene sentido que se muestre aca)
                if ($tag['id'] == 1) {
                    continue;
                }
                ?>
                <option 
                <?php echo ( $formValues['word_tag'] == $tag['id'] ) ? ' selected ' : '';?>
                value="<?php echo $tag['id'];?>"><?php echo $tag['name'];?></option>
            <?php endforeach; ?>
                
        </select>
        </p>
        <p>

            <span>Prioridad:</span>
            <?php \WordAdmin\Loader\printRadiosPriority("new_priority", "new_priority", $formValues['priority']);?>
        </p>
    </div>

    <div class="row">
        <p>
            <label for="field_word">Palabra:</label>
            <input type="text" id="field_word" name="field_word" required>
            <button id="btn_search" type="button">Buscar</button>
            <br>Primero busca en DB y despues hace query a jisho
        </p>
    </div>

    <p><input type="submit" name="new_word_save" value="Guardar" id="btn_word_save"></p>
	
    <label for="word_example">Ejemplo:</label>
    <textarea  name="new_example" id="word_example"></textarea>

	<section id="searchresults">
		
		<div id="searchresults-queried-wrapper"> 
            <p class="message"></p>     
            <div id="searchresults-queried"> 
				</div>
            </div>
				
		<div id="searchresults-other">
            <hr>
            <div id="searchresults-other-creation">

                <div id="results-jisho-wrapper">
                    <button id="btn_query_jisho" type="button">jisho.org</button><button id="btn_query_jisho_copy" type="button">jisho copiar</button><button id="btn_query_jisho_copysave" type="button">jisho copiar y guardar</button>
                    
                    <br>Seleccionar el de jisho y apretar en <kbd>jisho copiar</kbd>

                    <p class="message"></p>

                    <div id="results-jisho">      
                    </div>

                    
                </div>


                <div id="results-desde-cero">
                    <p>
                        <label for="custom_word">Palabra:</label>
                        <input type="text" id="custom_word" name="custom[word]">

                        <label for="custom_reading">Lectura:</label>
                        <input type="text" id="custom_reading" name="custom[reading]">
                    </p>
                    <p>
                        <label for="custom_meaning">Definición:</label><br>
                        <textarea id="custom_meaning" name="custom[meaning]"></textarea>
                    </p>
                </div>
            </div>
		</div>
	</section>






    
</form>


<?php 
echo \WordAdmin\Loader\templatePartLoadLib('jquery-local');
?>

<script type="text/javascript">

$(document).ready(function(){



// --------
// functionallity

$('#btn_search').on('click', function(){
    if ( $('#field_word').val() == "" ) {
        alert("Llenar campo");
        return;
    }
    $('#searchresults #searchresults-queried-wrapper .message').text("buscando...");

    //e.preventDefault();
    $.ajax({
        type: "POST",
        url: "",
        data: { search_word: $('#field_word').val() },
        dataType: "json",

        success: function(result){
            var allresult = result.res

            $('#searchresults-queried').empty();

            $('#searchresults #searchresults-queried-wrapper .message').text("");

            if (allresult.length > 0) {
                allresult.forEach(function(item){
                    var tags_priority = "";
                    if (item['highest_priority']) {
                        tags_priority += " pri:" + item['highest_priority'];
                    }
                    if (  item['word_tags'] ) {
                        tags_priority += " tag:" + item['word_tags'];
                    }



                    var item_input = '<input type="radio" name="queriedWord" value="'+ item['id']　+'" data-word="'+ item['word'] +'" data-reading="'+ item['reading'] +'" data-definition="'+ item['definition'] +'">';
                    var item_info = '<span class="check_in_anki in_anki--' + item['in_anki'] +'">(id:' + item['id'] + ')</span>' + item['word'] + "  【" + item['reading'] + "】 " + item['definition'];
                    var item_masinfo = ' <span class="extra-info">' + tags_priority +'</span>';

                    var item_final = item_input + item_info + item_masinfo;

                    if (item['deleted'] == true) {
                        item_final = '<del>' + item_final + '</del>';
                    }

                    $('#searchresults #searchresults-queried').append(
                        '<p class="word">' + item_final +'</p>'
                        );
                });

            }
            else {
                $('#searchresults #searchresults-queried-wrapper .message').text("Sin resultados");
                $('#searchresults-other-creation').show();
                $('button#btn_query_jisho').click();
            }


        }, error : function() {
                alert("Something went wrong!");
           }
    })
});




$('button#btn_query_jisho').on('click', function(){
    $('#searchresults-other .message').text("Cargando");
    $('#results-jisho').empty();
    var word_to_search = $('#field_word').val();

    //e.preventDefault();
    $.ajax({
        type: "post",
        url: "#",
        data: { queryJisho: word_to_search },
        dataType: "json",

        success: function(result){

            var allresult = result.res

            $('#results-jisho').empty();
            $('#searchresults-other .message').text("");

            if (allresult.length > 0) {
                allresult.forEach(function(item){

                    if ( item['word'] == null ) {
                        return;
                    }

            
                    $('#results-jisho').append(
                        
                        '<p class="word"><input type="radio" name="queriedJisho" value="'+ item['slug'] + '" data-word="' + item['word'] + '" data-reading="'+ item['reading'] + '"data-definition="'+ item['definition'] +'" > ' + item['word'] + " 【" + item['reading'] + "】 " + item['definition'] + "</p>"
                        );
                });

            }
            else {
                $('#searchresults-other .message').text("Sin resultados");
            }
        }, error : function() {
                alert("Something went wrong!");
           }
    })
});




// dirty. Para evitar hacer query a jisho nuevamente, cada vez que se modifica la selección 
// de los resultados de jisho, actualizar lo de campos custom. Así usar eso en proceso PHP
// se pone por boton porque por ahora no se esta pudiendo hacer el binding 
// con los radio creados dinamicamente (!)

$("#btn_query_jisho_copy").on("click", function(){
    var jishoEntry_word    = $('#results-jisho input[name="queriedJisho"]:checked').data('word');
    var jishoEntry_reading = $('#results-jisho input[name="queriedJisho"]:checked').data('reading');
    var jishoEntry_meaning = $('#results-jisho input[name="queriedJisho"]:checked').data('definition');

    $("#custom_word").val(jishoEntry_word);
    $("#custom_reading").val(jishoEntry_reading);
    $("#custom_meaning").val(jishoEntry_meaning);
});


$("#btn_query_jisho_copysave").on("click", function(){
    $("#btn_query_jisho_copy").click();
    $("#btn_word_save").click();

});

$('#form_input').submit(function(e) {
    // check que haya algo seleccionado antes de enviar 

    var db_word = $('#searchresults input[name="queriedWord"]:checked').length;
    var jisho_word = $('#results-jisho input[name="queriedJisho"]:checked').length;
    var jisho_word_text = $("#custom_word").val();

    if (db_word == 0 && jisho_word == 0) {
        // nada select, no dejar hacer el flujo
        alert("Seleccionar alguna opción");
        return false;
    }

    if (db_word == 0 && jisho_word == 1 && jisho_word_text == "") {
        alert("Si es de jisho, hay que copiar los datos");
        return false;
    }

    if (db_word == 1 || (jisho_word == 1 && jisho_word_text != "")) {
        // flujo normal
        $(this).submit();
    }
    else {
        e.preventDefault();
        alert("oops, pasó algo");
    }

    

});





});  // docready



</script>