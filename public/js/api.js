const h3 = document.querySelectorAll('h3');


for(i=0; i<h3.length;i++){
    if(h3[i].getAttribute('data-zone')=='Legion'){
        h3[i].classList.add('legion')
    }

    if(h3[i].getAttribute('data-zone')=='Battle for Azeroth'){
        h3[i].classList.add('bfa')
    }

    if(h3[i].getAttribute('data-zone')=='Shadowlands'){
        h3[i].classList.add('shadowland')
    }

    if(h3[i].getAttribute('data-zone')=='Warlords of Draenor'){
        h3[i].classList.add('wod')
    }

    if(h3[i].getAttribute('data-zone')=='Mists of Pandaria'){
        h3[i].classList.add('pandaria')
    }

    if(h3[i].getAttribute('data-zone')=='Guilde'){
        h3[i].classList.add('guilde')
    }

    if(h3[i].getAttribute('data-zone')=='Cataclysm'){
        h3[i].classList.add('cataclysm')
    }
}

