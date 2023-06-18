require('./bootstrap');

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

let sidebarButton = document.querySelectorAll('.accordion-toggle');
sidebarButton.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        let thisButton = e.target.getAttribute("aria-expanded");
        let thisAction = false;
        if (thisButton == 'true' || thisButton == true) {
            thisAction = false;
            e.target.nextElementSibling.classList.remove('show');
        } else {
            thisAction = true;
            e.target.nextElementSibling.classList.add('show');
        }
        e.target.setAttribute("aria-expanded", thisAction);
        console.log(e.target.nextElementSibling);
    });
})

let transportiInput = document.querySelectorAll('.transporti input[type="checkbox"]');
transportiInput.forEach(transport => {
    transport.addEventListener('change', e => {
        let thisInput = e.target;
        let transChecked = thisInput.checked;
        let transVal = thisInput.value;
        let transportParent = thisInput.parentElement.parentElement;
        let transId = transportParent.id;
        if (transVal == '2' && transChecked) {
            transportParent.querySelector('.transportShuma').style.display = "block";
        } else if (transVal == '2' && !transChecked) {
            transportParent.querySelector('.transportShuma').style.display = "none";
        }
        transportType(transVal, transId)
    })
})

function transportType(transVal, transId) {
    let tranText = '';
    if (transId) {
        if (transVal == '1') {
            document.querySelector("#" + transId + "-2").checked = false;
            document.querySelector("#" + transId + "-3").checked = false;
            document.querySelector("#" + transId + " .transportShuma").style.display = "none";
        } else if (transVal == '2') {
            document.querySelector("#" + transId + "-1").checked = false;
        } else if (transVal == '3') {
            document.querySelector("#" + transId + "-1").checked = false;
        }
    }
};

let workHoursInputs = document.querySelectorAll('.workhours input[type="checkbox"]');
workHoursInputs.forEach(workHours => {
    let thisElem = workHours.parentElement.parentElement.parentElement.parentElement;
    if (workHours.checked) {
        thisElem.querySelector('.hcheckbox').classList.remove('show');
    } else {
        thisElem.querySelector('.hcheckbox').classList.add('show');
    }
    workHours.addEventListener('change', e => {
        let thisInput = e.target.parentElement.parentElement.parentElement.parentElement;
        if (e.target.checked) {
            thisInput.querySelector('.hcheckbox').classList.remove('show');
        } else {
            thisInput.querySelector('.hcheckbox').classList.add('show');
        }
    })
})

let timeInputs = document.querySelectorAll('.inputtimes');
timeInputs.forEach(minIput => {
    minIput.addEventListener('change', e => {
        let thisInput = e.target;
        let parentInput = thisInput.parentElement;
        let minELem = parentInput.querySelector('.input-hmin').value;

        if (minELem > 23) {
            parentInput.querySelector('.input-hmin').value = 23;
            minELem = 23;
        }
        if (minELem < 0) {
            parentInput.querySelector('.input-hmin').value = '00';
            minELem = '00';
        }
        let maxELem = parentInput.querySelector('.input-hmax').value;

        if (maxELem > 59) {
            parentInput.querySelector('.input-hmax').value = 59;
            maxELem = 59;
        }
        if (maxELem < 0) {
            parentInput.querySelector('.input-hmax').value = '00';
            maxELem = '00';
        }
        if (minELem && maxELem) {
            parentInput.querySelector('.input-base').value = minELem + ':' + maxELem;
        }
    })
})

// let deleteButtons = document.querySelectorAll('.deleteModal');
let deleteModal = document.querySelector('#deleteModal');
let closeModals = document.querySelectorAll('.closeModal');
// deleteButtons.forEach(deleteButton => {
//     deleteButton.addEventListener('click', (e) => {
//         deleteModal.classList.add('show');
//         deleteModal.querySelectorAll('.modalType').forEach(modaltype => {
//             modaltype.innerHTML = e.target.dataset.type;
//         })
//         deleteModal.querySelector('.modalText').innerHTML = e.target.dataset.text;
//         deleteModal.querySelector('.deleteLink').href = e.target.dataset.link;
//     });
// })
window.deleteModalF = (e) => {
    deleteModal.classList.add('show');
    deleteModal.querySelectorAll('.modalType').forEach(modaltype => {
        modaltype.innerHTML = e.dataset.type;
    })
    deleteModal.querySelector('.modalText').innerHTML = e.dataset.text;
    deleteModal.querySelector('.deleteLink').href = e.dataset.link;
}
closeModals.forEach(closeModal => {
    closeModal.addEventListener('click', e => {
        deleteModal.classList.remove('show');
    })
});

let openMenu = document.querySelector('.header .menu-toggle-button .nav-link');
let bgOverlay = document.querySelector('.bg_overlay');
let bodyP = document.getElementsByTagName('body');
if (bodyP) {
    bodyP = bodyP[0];
}
if (openMenu) {
    console.log(bodyP)
    openMenu.addEventListener('click', e => {
        if (e.target.classList.contains('show')) {
            e.target.classList.remove('show');
            bodyP.classList.remove('sidebar');
        } else {
            e.target.classList.add('show');
            bodyP.classList.add('sidebar');
        }
    })
}
if (bgOverlay) {
    bgOverlay.addEventListener('click', e => {
        if (bodyP.classList.contains('sidebar')) {
            openMenu.classList.remove('show');
            bodyP.classList.remove('sidebar');
        }
    })
}