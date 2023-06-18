import flatpickr from "flatpickr";
import { Albanian } from "flatpickr/dist/l10n/sq.js";

flatpickr.localize(Albanian);
var today = new Date();
let flatPickrsDate = document.querySelectorAll('.flatpickr.date');
let flatPickrsDateTime = document.querySelectorAll('.flatpickr.datetime');
let optionsD = {
    altInput: true,
    altFormat: "j F Y",
    dateFormat: "Y-m-d",
    minDate: "2021-01",
    locale: {
        firstDayOfWeek: 1
    },
}
let optionsT = {
    altInput: true,
    enableTime: true,
    altFormat: "j F Y H:i",
    dateFormat: "Y-m-d H:i",
    minDate: "2021-01",
    locale: {
        firstDayOfWeek: 1
    },
}
flatPickrsDate.forEach(flatPickrDate => {
    // if(flatPickrDate.classList.contains('tomorrow')){
    //     let tomorrow = new Date(today);
    //     tomorrow.setDate(tomorrow.getDate() + 1)
    //     options.defaultDate = tomorrow;
    // }
    flatpickr(flatPickrDate, optionsD);
})
flatPickrsDateTime.forEach(flatPickrDateTime => {
    // if(flatPickrDate.classList.contains('tomorrow')){
    //     let tomorrow = new Date(today);
    //     tomorrow.setDate(tomorrow.getDate() + 1)
    //     options.defaultDate = tomorrow;
    // }
    flatpickr(flatPickrDateTime, optionsT);
})