formFirst = document.getElementById("form-first-part");
reserverButton = document.getElementById("reserver-button");
formSecond = document.getElementById("form-second-part");
backButton = document.getElementById("back-reservation-button");
confirmButton = document.getElementById("confirmer-reservation-button");

reservationForm=document.getElementById("reservationForm");

formFirst.hidden = false;
reserverButton.hidden = false;
formSecond.hidden = true;
backButton.hidden = true;
confirmButton.hidden = true;

couverts = document.getElementById('recap-couverts')
date = document.getElementById('recap-date')
heure = document.getElementById('recap-heure')
lieu = document.getElementById('recap-lieu')

const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
const month = ['Jan','Fev','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Dec',]

today = (new Date).getFullYear()+"-"+(((new Date).getMonth()+1).toString())+"-"+((new Date).getDate())

function reserverPartUn(){
    data = new FormData(reservationForm)
    event.preventDefault();

    if(
        ((new Date(today).getTime()) == (new Date(data.get('date'))).setHours(0)) &&
        (((new Date).getHours()-1 >= parseInt(data.get('heure').substring(2, 4) )) ||
        ((new Date).getMinutes() >= parseInt(data.get('heure').substring(5, 7)) && (new Date).getHours()-1 >= parseInt(data.get('heure').substring(2, 4) )))
    ){
        alert('Toute réservation doit se faire au minimum une heure à l\'avance \n pour réserver après ce délai veuillez nous contacter au 02 38 53 71 12')
    }
    else if (data.get('date') == "") {
        alert("veuillez choisir une date")
    }
    else {
        formFirst.hidden = true;
        reserverButton.hidden = true;
        formSecond.hidden = false;
        backButton.hidden = false;
        confirmButton.hidden = false;
        

        couverts.innerHTML = data.get('couverts')

        chosenDate = new Date(data.get('date'))
        date.innerHTML = days[chosenDate.getDay()] + ", " + chosenDate.getDate() + " " + month[chosenDate.getMonth()]
    
        heure.innerHTML = data.get('heure').substring(2, 7)

        lieu.innerHTML = data.get('salle')
    }
}

function backReservation() {
    event.preventDefault();
    formFirst.hidden = false;
    reserverButton.hidden = false;
    formSecond.hidden = true;
    backButton.hidden = true;
    confirmButton.hidden = true;
}


(document.getElementById("date-picker")).addEventListener("blur", ()=> {
    chosenDate = document.getElementById("date-picker").value
    chosenDate = new Date(chosenDate)
    if (chosenDate.getDay() == 1 || chosenDate.getDay() == 0) {
        alert('L\'établissement est fermé les Lundis et Samedis')
        document.getElementById("date-picker").value = ""
    }
})

if ((new Date((document.getElementById("date-picker")).value)).getDay()==1 || (new Date((document.getElementById("date-picker")).value)).getDay==0) {
    document.getElementById("date-picker").value = ""
}

function ConfirmerReservation() {
    console.log(new FormData(document.getElementById('reservationForm')))
}