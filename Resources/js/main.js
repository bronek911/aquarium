$(function(){

    var addButton = $('div.addTankButton');

    addButton.on('click', function () {
        document.location.href = './new';
    });

    var baseTanks = $('div.tank');

    baseTanks.on('click', function(){
        var id = $(this).data('id');
        document.location.href = './'+id;
    })

    baseTanks.contextmenu(function() {
        //alert( "Handler for .contextmenu() called." );
        $(this).toggleClass('selected');
        return false;
    });

});