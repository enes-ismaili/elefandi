import SlimSelect from 'slim-select'

new SlimSelect({
    select: '#selectBrands',
    placeholder: 'Zgjidhni Markën',
    closeOnSelect: false,
    searchText: 'Nuk u gjet asnjë markë',
    searchPlaceholder: 'Kërko',
    allowDeselect: true
})
let advancedVariants = true;
let priceCombination = document.getElementById('price_combination');
let addTableVariant = document.getElementById('price_combination_table');
let currentVariantsFull = [];

let tagNum = 0;
let addCustomerOption = document.getElementById('customer_choice_options');
let atributeOldValue = 0;
let currentValues = [];
let currentValuesFull = [];
let currentValues1 = [];

function removeEvent(el, type, handler) {
    if (el.detachEvent) {
        el.detachEvent('on' + type, handler);
    } else {
        el.removeEventListener(type, handler);
    }
}
let activeAdvancedAttribute = document.querySelector('#colors_active');
activeAdvancedAttribute.addEventListener('change', (e) => {
    console.log(e.target.checked);
    advancedVariants = e.target.checked;
    if (advancedVariants) {
        document.querySelector('.form-attributes').classList.add('show');
    } else {
        document.querySelector('.form-attributes').classList.remove('show');
    }
    colorChange();
});
let variantStock = 0;

function getAllTags() {
    let thisTags = document.querySelectorAll('.customer_choice_options .tagger ul');
    thisTags.forEach(tag => {
        if (!tag.getAttribute('listener')) {
            tag.setAttribute('listener', true)
            tag.addEventListener('click', (e) => {
                if (e.target.nodeName == 'A') {
                    colorChange();
                    console.log('tesaas');
                }
            });
            tag.addEventListener('keydown', (e) => {
                if (e.keyCode == 188 || e.keyCode == 13) {
                    getCurrentTable();
                    addTableVariant.innerHTML = '';
                    if (currentVariantsFull.length >= 1) {
                        variantStock = 0;
                        currentVariantsFull.forEach(variant => {
                            addVariantStock(variant.text + '-', variant.value + '-');
                        });
                    } else {
                        addVariantStock();
                    }
                }
            });
        }
    })
}

function colorChange() {
    getCurrentTable();
    addTableVariant.innerHTML = '';
    if (currentVariantsFull.length >= 1) {
        variantStock = 0;
        currentVariantsFull.forEach(variant => {
            addVariantStock(variant.text + '-', variant.value + '-');
        });
    } else {
        addVariantStock();
    }
}
let tableElem = `
<div class="price_combination" id="price_combination">
    <table class="table table-bordered aiz-table footable footable-8 breakpoint-xl">
        <thead>
            <tr class="footable-header">
                <td class="text-center footable-first-visible" style="display: table-cell;">Varianti</td>
                <td class="text-center" style="display: table-cell;">Çmimi i Variantit</td>
                <td class="text-center" data-breakpoints="lg" style="display: table-cell;">SKU</td>
                <td class="text-center" data-breakpoints="lg" style="display: table-cell;">Stoku</td>
                <td class="text-center table-variant_image" data-breakpoints="lg" style="display: table-cell;">Foto</td>
                <td class="footable-last-visible table-actions" style="display: table-cell;"></td>
            </tr>
        </thead>
        <tbody id="price_combination_table">
        </tbody>
    </table>
</div>
`;

function addVariantStock(color = '', colorid = '', variantNum = 0) {
    let currentValuesSelected = [];
    if (advancedVariants) {
        currentValuesFull.forEach(attri => {
            let selectedAtribute = document.querySelector('#variants-option-' + attri.value).value;
            if (selectedAtribute) {
                currentValuesSelected.push(attri);
            }
        });
        if (currentValuesSelected.length > 0) {
            if (currentValuesSelected.length > variantNum) {
                let currentAttribute = currentValuesSelected[variantNum];
                let currentAtribute = document.querySelector('#variants-option-' + currentAttribute.value).value;
                if (currentAtribute) {
                    let allTags = currentAtribute.split(',');
                    variantStock++;
                    if (currentValuesSelected.length > variantNum + 1) {
                        allTags.forEach(tags => {
                            let currentAttribute1 = currentValuesSelected[variantNum + 1];
                            let currentAtribute1 = document.querySelector('#variants-option-' + currentAttribute1.value).value;
                            if (currentAtribute1) {
                                variantStock++;
                                let allTags1 = currentAtribute1.split(',');
                                if (currentValuesSelected.length > variantNum + 2) {
                                    allTags1.forEach(tags1 => {
                                        let currentAttribute2 = currentValuesSelected[variantNum + 2];
                                        let currentAtribute2 = document.querySelector('#variants-option-' + currentAttribute2.value).value;
                                        if (currentAtribute2) {
                                            let allTags2 = currentAtribute2.split(',');
                                            if (currentValuesSelected.length > variantNum + 3) {
                                                allTags2.forEach(tags2 => {
                                                    let currentAttribute3 = currentValuesSelected[variantNum + 3];
                                                    let currentAtribute3 = document.querySelector('#variants-option-' + currentAttribute3.value).value;
                                                    if (currentAtribute3) {
                                                        let allTags3 = currentAtribute3.split(',');
                                                        if (currentValuesSelected.length > variantNum + 4) {
                                                            allTags3.forEach(tags3 => {
                                                                let currentAttribute4 = currentValuesSelected[variantNum + 4];
                                                                let currentAtribute4 = document.querySelector('#variants-option-' + currentAttribute4.value).value;
                                                                if (currentAtribute4) {
                                                                    let allTags4 = currentAtribute4.split(',');
                                                                    if (currentValuesSelected.length > variantNum + 5) {
                                                                        allTags4.forEach(tags4 => {
                                                                            addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3 + '-' + tags4, colorid + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3 + '-' + tags4));
                                                                        });
                                                                    } else {
                                                                        allTags4.forEach(tags4 => {
                                                                            addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3 + '-' + tags4, colorid + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3 + '-' + tags4));
                                                                        });
                                                                    }
                                                                }
                                                            });
                                                        } else {
                                                            allTags3.forEach(tags3 => {
                                                                addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3, colorid + tags.trim() + '-' + tags1 + '-' + tags2 + '-' + tags3));
                                                            });
                                                        }
                                                    }
                                                });
                                            } else {
                                                allTags2.forEach(tags2 => {
                                                    addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim() + '-' + tags1 + '-' + tags2, colorid + tags.trim() + '-' + tags1 + '-' + tags2));
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    allTags1.forEach(tags1 => {
                                        addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim() + '-' + tags1, colorid + tags.trim() + '-' + tags1));
                                    });
                                }
                            } else {
                                variantNum++;
                                addVariantStock(color, colorid, variantNum);
                            }
                        })
                    } else {
                        allTags.forEach(tags => {
                            addTableVariant.insertAdjacentHTML('beforeend', addTableRow(colorid, color + tags.trim(), colorid + tags.trim()));
                        })
                    }
                    priceCombination.classList.add('show');
                } else {
                    variantNum++;
                    addVariantStock(color, colorid, variantNum);
                }
            }
        } else {
            if (color) {
                let newColor = color.slice(0, -1);
                let newColorId = colorid.slice(0, -1);
                addTableVariant.insertAdjacentHTML('beforeend', addTableRow(newColorId, newColor, newColorId));
                priceCombination.classList.add('show');
            } else {
                if (priceCombination.classList.contains('show')) {
                    priceCombination.classList.remove('show');
                }
            }
        }
    } else {
        if (color) {
            let newColor = color.slice(0, -1);
            let newColorId = colorid.slice(0, -1);
            addTableVariant.insertAdjacentHTML('beforeend', addTableRow(newColorId, newColor, newColorId));
            priceCombination.classList.add('show');
        } else {
            if (priceCombination.classList.contains('show')) {
                priceCombination.classList.remove('show');
            }
        }
    }
    updateInputFile();
}
let proceCombinationOld = document.querySelector('#price_combination_table');
let newArrayVariants = [];

function getCurrentTable() {
    newArrayVariants = [];
    proceCombinationOld = document.querySelector('#price_combination_table');
    let variantss = proceCombinationOld.children;
    Array.from(variantss).forEach(variant => {
        let variantId = variant.querySelector('.svariant_id').value;
        let variantName = variant.querySelector('.svariant_name').value;
        let variantPrice = variant.querySelector('.svariant_price').value;
        let variantSku = variant.querySelector('.svariant_sku').value;
        let variantQty = variant.querySelector('.svariant_qty').value;
        let variantImg = variant.querySelector('.svariants_image').value;
        newArrayVariants['s' + variantId] = [];
        newArrayVariants['s' + variantId]['variant_id'] = variantId;
        newArrayVariants['s' + variantId]['variant_name'] = variantName;
        newArrayVariants['s' + variantId]['variant_price'] = variantPrice;
        newArrayVariants['s' + variantId]['variant_sku'] = variantSku;
        newArrayVariants['s' + variantId]['variant_qty'] = variantQty;
        newArrayVariants['s' + variantId]['variant_img'] = variantImg;
    })
}

function getCurrentTableData() {
    let newArrayVariantss = newArrayVariants;
    return newArrayVariantss;
}

let colorsSelect = new SlimSelect({
    select: '#selectColors',
    placeholder: 'Zgjidhni Ngjyrat',
    closeOnSelect: false,
    searchText: 'Nuk u gjet asnjë ngjyre',
    searchPlaceholder: 'Kërko',
    beforeOnChange: (info) => {
        currentVariantsFull = info;
        let currentVariantsFullOrder = currentVariantsFull.sort(function(a, b) {
            return a.value - b.value;
        });
        currentVariantsFull = currentVariantsFullOrder;
        colorChange();
    },
    onChange: (info) => {
        if (info.length > currentVariantsFull.length) {
            info.forEach((currentValue) => {
                currentVariantsFull.push(currentValue);
                insertColor();
            })
        }
    }
})

function insertColor() {
    if (advancedVariants) {
        colorChange();
    } else {
        colorChange();
    }
}

function findDifferent(value) {
    currentValues1.splice(currentValues1.indexOf(value.id), 1)
}
let attributeSelect = new SlimSelect({
    select: '#selectAttribute',
    placeholder: 'Zgjidhni Atributet',
    closeOnSelect: false,
    searchText: 'Nuk u gjet asnjë atribut',
    searchPlaceholder: 'Kërko',
    beforeOnChange: (info) => {
        if (info.length > atributeOldValue) {
            atributeOldValue++;
            let currentValue = info[info.length - 1];
            addCustomerOption.insertAdjacentHTML('beforeend', addHtml(currentValue.text, currentValue.value));
            currentValues.push(currentValue.value);
            currentValuesFull.push(currentValue);
            var input = document.querySelector('#variants-option-' + currentValue.value);
            var tags = tagger(input, {
                allow_duplicates: false,
                allow_spaces: true,
                wrap: true,
                completion: {
                    list: []
                }
            });
            getAllTags();
        } else {
            atributeOldValue--;
            currentValues1 = currentValues.slice(0);
            info.filter(x => findDifferent(x));
            currentValues.splice(currentValues.indexOf(currentValues1[0]), 1)
            currentValuesFull.splice(currentValuesFull.findIndex(obj => obj.value == currentValues1[0]), 1)
            console.log('attr');
            insertColor()
        }
    },
    onChange: (info) => {
        console.log('atchange')
        if (info.length > currentValuesFull.length) {
            info.forEach((currentValue) => {
                atributeOldValue++;
                // addCustomerOption.insertAdjacentHTML('beforeend', addHtml(currentValue.text, currentValue.value))
                currentValues.push(currentValue.value);
                currentValuesFull.push(currentValue);
                var input = document.querySelector('#variants-option-' + currentValue.value);
                var tags = tagger(input, {
                    allow_duplicates: false,
                    allow_spaces: true,
                    wrap: true,
                    completion: {
                        list: []
                    }
                });
                getAllTags();
            })
        }
    }
})

function addHtml(name, id) {
    let inputId = 'attr-' + name;
    tagNum++;
    return `
    <div class="form-group row variants-option-${id}">
    <div class="col-md-3">
        <input type="hidden" name="choice_no[]" value="1">
        <input type="text" class="form-control" name="choice[]" value="${name}" placeholder="Choice Title" readonly="">
    </div>
    <div class="col-md-8">
        <input type="text" value="" id="variants-option-${id}" class="variant-input" name="variant_attributes[${id}]" />
    </div>
    </div>`;
}

function addTableRow(color, name, id) {
    let currentPrice = document.querySelector('#productprice').value;
    let currentSku = document.querySelector('#productstock').value;
    // let oldPriceTable = getCurrentTableData();
    let nPrice = '';
    let nSku = '';
    let nQty = '';
    let nImage = '';
    let hasImage = false;
    let imageHtml = '';
    // if (oldPriceTable && oldPriceTable['s' + id]) {
    //     let oldTableDatas = oldPriceTable['s' + id];
    //     nPrice = oldTableDatas['variant_price'];
    //     nQty = oldTableDatas['variant_qty'];
    //     nSku = oldTableDatas['variant_sku'];
    //     nImage = oldTableDatas['variant_img'];
    // }
    console.log(id)
    if (window.existVariants) {
        let thisVar = window.existVariants['v' + id];
        console.log(id);
        console.log(thisVar)
        if (thisVar) {
            nPrice = thisVar['price'];
            nQty = thisVar['qty'];
            nSku = thisVar['sku'];
            nImage = thisVar['img'];
            if (nImage) {
                hasImage = true;
                imageHtml = `<div class="remove-image"><i class="fas fa-times"></i></div>
                <img src="https://new57.elefandi.com/photos/${nImage}">`;
            }
        }
    }
    return `
    <tr class="variant" id="table-price-${id}">
        <td class="footable-first-visible c${color}" style="display: table-cell;">
            <label for="" class="control-label">${name}</label>
            <input type="hidden" name="variant_id[]" value="${id}" class="svariant_id">
            <input type="hidden" name="variant_name[]" value="${name}" class="svariant_name">
        </td>
        <td style="width: 160px;">
            <input type="number" lang="en" name="variant_price[]" value="${nPrice}" min="0" step="0.01" class="form-control variant_price svariant_price" placeholder="${currentPrice}">
        </td>
        <td style="width: 130px;">
            <input type="text" name="variant_sku[]" value="${nSku}"" class="form-control svariant_sku">
        </td>
        <td style="width: 100px;">
            <input type="number" lang="en" name="variant_qty[]" value="${nQty}" min="0" step="1" class="form-control variant_qty svariant_qty" placeholder="${currentSku}">
        </td>
        <td style="display: table-cell;">
            <div class="input-group table-variant_image ${(hasImage)?'upload':''}" data-toggle="aizuploader" data-type="image">
                <label for="variant_image_${id}">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div>
                    </div>
                    <div class="form-control file-amount text-truncate">Choose file</div>
                </label>
                <div class="view_image">${imageHtml}</div>
                <input type="file" class="variants_images" id="variant_image_${id}" hidden>
                <input type="hidden" name="variant_img[]" class="selected-files svariants_image" value="${nImage}">
            </div>
            <div class="file-preview box sm"></div>
        </td>
        <td class="footable-last-visible" style="display: table-cell;">
            <button type="button" class="btn btn-icon btn-sm btn-danger"
                onclick="delete_variant(this)"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>`;
}

function updateInputFile() {
    let allFileSelect = document.querySelectorAll('.variants_images');
    console.log('updateinput')
    console.log(allFileSelect)
    allFileSelect.forEach(input => {
        if (!input.getAttribute('listener')) {
            input.setAttribute('listener', true)
            input.addEventListener('change', (e) => {
                console.log(e);
                // previewFile(e.target, e.target.files[0])
                window.uploadVariantImage(e.target, e.target.files[0]);
            })
        }
    });
}

function uploadFile(input, file) {
    let url = "https://new57.elefandi.com/upload/image";
    let formData = new FormData();
    formData.append("file", file);
    fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            previewFile(input, data.file)
            input.parentElement.querySelector('.selected-files').value = data.name;
        })
        .catch((e) => {
            console.log(e);
        });
}



document.addEventListener('DOMContentLoaded', function() {
    attributeSelect.set(window.selectedAttribute);
    colorsSelect.set(window.selectedColors);
    updateInputFile();
}, false);
document.getElementById('productprice').addEventListener('change', function(e) {
    let prodPrice = e.target.value;
    let allVariantsPrice = document.querySelectorAll('#price_combination_table .variant_price');
    allVariantsPrice.forEach(e => {
        if (!e.value || e.value == 0) {
            e.placeholder = prodPrice;
        }
    })
}, false);
document.getElementById('productstock').addEventListener('change', function(e) {
    let prodStock = e.target.value;
    let allVariantsStock = document.querySelectorAll('#price_combination_table .variant_qty');
    allVariantsStock.forEach(e => {
        if (!e.value || e.value == 0) {
            e.placeholder = prodStock;
        }
    })
}, false);