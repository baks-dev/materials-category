/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */


let formName = document.forms.category_material_form;


/** Коллекция СЕКЦИЙ для свойств продукта*/

/* кнопка Добавить коллекцию */
//var $addButton = document.querySelector('button.add_collection');
let $addButtonSection = document.getElementById('section_addCollection');

/* Блок для новой коллекции */
let $blockCollection = document.getElementById('section_collection');

if($blockCollection)
{
    // /* добавить событие на удаление ко всем существующим элементам формы в блок с классом .del-item */
    // let $delItem = $blockCollection.querySelectorAll('.del-item-section');
    //
    // /* Удаляем при клике колекцию СЕКЦИЙ */
    // $delItem.forEach(function (item) {
    //     item.addEventListener('click', function () {
    //
    //         let $counter = $blockCollection.getElementsByClassName('item-collection-section').length;
    //         if ($counter > 1) {
    //             item.closest('.item-collection-section').remove();
    //         }
    //         else
    //         {
    //             alert('Минимально должна быть добавлена одна секция');
    //         }
    //     });
    // });

    /* добавить событие на удаление ко всем существующим секциям свойств */
    deleteSection($blockCollection);

    /* добавить событие на удаление свойства из секции */
    deleteField($blockCollection)


    /* Существующие кнопки Добавить поле в секцию */
    let $addButtonField = document.querySelectorAll('[id^="createSectionField"]');
    $addButtonField.forEach(function(item)
    {
        createSectionField(item.dataset.section);
    });


    // /* Удаляем при клике колекцию ПОЛЕЙ */
    // /* добавить событие на удаление ко всем существующим элементам формы в блок с классом .del-item */
    // let $delItemField = $blockCollection.querySelectorAll('.del-item-field');
    //
    // /* Удаляем при клике колекцию ПОЛЕЙ */
    // $delItemField.forEach(function (item) {
    //     item.addEventListener('click', function () {
    //
    //         let $countField = item.closest('.item-collection-field').parentNode.getElementsByClassName('item-collection-field').length;
    //
    //         if ($countField > 1) {
    //             item.closest('.item-collection-field').remove();
    //         }
    //         else
    //         {
    //             alert('Минимально должно быть добавлено одно поле');
    //         }
    //     });
    // });


    /* получаем количество коллекций и присваиваем data-index прототипу */
    //$blockCollection.dataset.sectionIndex = $blockCollection.getElementsByClassName('item-collection-section').length.toString();

    /**
     *
     * Добавить коллекцию СВОЙСТВ ПРОДУКТА
     *
     *
     */

    /* Добавляем новую коллекцию */
    $addButtonSection.addEventListener('click', function()
    {
        /* получаем прототип коллекции  */
        let newForm = this.dataset.prototype;
        let index = this.dataset.index * 1;


        /* Замена '__name__' в HTML-коде прототипа на
         вместо этого будет число, основанное на том, сколько коллекций */
        newForm = newForm.replace(/__category_section__/g, index);
        //newForm = newForm.replace(/__FIELD_SORT__/g, 100);
        //newForm = newForm.replace(/__FIELDS_INDEX__/g, 1);


        /* Вставляем новую коллекцию */
        let div = document.createElement('div');
        div.id = 'item-collection-section-' + index;

        div.classList.add('card');
        div.classList.add('p-4');
        div.classList.add('mb-3');
        div.classList.add('border-light');
        div.classList.add('item-collection-section');


        div.innerHTML = newForm;
        $blockCollection.append(div);


        /* Плавная прокрутка к элементу */
        div.scrollIntoView({block: "center", inline: "center", behavior: "smooth"});

        // let field = document.getElementById('field-collection-' + index);
        // field.innerHTML = field.innerHTML.replace(/__FIELDS__/g, '0')
        //     .replace(/__FIELD_SORT__/g, '100');


        /* Добавить поле в секцию */
        createSectionField(index);


        /* Удаляем при клике СЕКЦИЮ */
        deleteSection(div);
        /* Удаляем при клике СВОЙСТВО */
        deleteField(div);

        /* Увеличиваем data-index на 1 после вставки новой коллекции */
        this.dataset.index = (index + 1).toString();

    });
}


/** Добавить секцию  */
function deleteSection($block)
{

    let $delItem = $block.querySelectorAll('.del-item-section');

    /* Удаляем при клике колекцию СЕКЦИЙ */
    $delItem.forEach(function(item)
    {
        item.addEventListener('click', function()
        {

            let $counter = $block.getElementsByClassName('item-collection-section').length;

            if($counter > 1)
            {
                item.closest('.item-collection-section').remove();
            } else
            {
                alert('Минимально должна быть добавлена одна секция');
            }
        });
    });
}

/** Удалить свойство из секции  */
function deleteField($block)
{

    $delItem = $block.querySelectorAll('.del-item-field');

    /* Удаляем при клике свойство из секции */
    $delItem.forEach(function(item)
    {
        item.addEventListener('click', function()
        {

            let $fieldCollection = document.getElementById('field-collection-' + this.dataset.section);
            let $counter = $fieldCollection.querySelectorAll('.item-collection-field').length;

            if($counter > 1)
            {
                item.closest('.item-collection-field').remove();
            } else
            {
                alert('Минимально должна быть добавлена одна секция');
            }
        });
    });
}


/** Добавить свойство в секцию  */
function createSectionField(section)
{

    /* Событие на клик добавления полей в секцию */
    let $btnAddFields = document.getElementById('createSectionField' + section);

    //$btnCreateSectionField = div.querySelector('#createSectionField'+ index);
    $btnAddFields.addEventListener('click', function()
    {

        //$btnAddFields = document.getElementById('createSectionField'+section)

        let section_id = $btnAddFields.dataset.section;

        /* получаем прототип коллекции  */
        let newForm = $btnAddFields.dataset.prototype;

        let index = $btnAddFields.dataset.index;


        /* Замена '__name__' в HTML-коде прототипа на
         вместо этого будет число, основанное на том, сколько коллекций */
        newForm = newForm.replace(/__category_section__/g, section_id);
        newForm = newForm.replace(/__section_field__/g, index);
        newForm = newForm.replace(/__FIELD_SORT__/g, index * 10 + 100);


        /* Вставляем новую коллекцию */
        let div = document.createElement('div');
        div.id = 'item-collection-field-' + index;
        div.classList.add('item-collection-field');
        div.classList.add('pb-3');

        div.innerHTML = newForm;

        document.getElementById('field-collection-' + section_id).append(div);


        /* Плавная прокрутка к элементу */
        div.scrollIntoView({block: "center", inline: "center", behavior: "smooth"});

        /* Удаляем при клике СВОЙСТВО */
        deleteField(div);

        $btnAddFields.dataset.index = (index * 1 + 1).toString();

        //div.innerHTML = newForm;
        //$blockCollection.append(div);

    });

}


// function createField($blockCollectionFields, index) {
//     /* получаем прототип коллекции  */
//     let newFormField = $blockCollectionFields.dataset.prototype;
//     let index_field = $blockCollectionFields.dataset.index * 1 + 1;
//
//     /* Замена '__name__' в HTML-коде прототипа на
//     вместо этого будет число, основанное на том, сколько коллекций */
//     newFormField = newFormField.replace(/__SECTION__/g, index);
//     newFormField = newFormField.replace(/__FIELD__/g, index_field);
//
//     /* Вставляем новую коллекцию */
//     let div = document.createElement('div');
//     div.innerHTML = newFormField;
//     $blockCollectionFields.append(div);
//
//     /* Удаляем при клике колекцию */
//     div.querySelector('.del-item-field').addEventListener('click', function () {
//         let $countField = div.parentNode.getElementsByClassName('item-collection-field').length;
//         if ($countField > 1) {  div.remove(); } /* Удаляем блок */
//     });
//
//     /* Увеличиваем data-index на 1 после вставки новой коллекции */
//     $blockCollectionFields.dataset.index = index_field;
// }


document.querySelectorAll('.is-reference').forEach(function(isReference)
{

    /* Обрабатываем уже существующие и сохраненные ТП */
    chanfeReferenc(isReference);

    isReference.addEventListener('change', function()
    {
        chanfeReferenc(this);
    });

});


/** Получаем поле 'Название раздела' по локали для 'Символьный код категории' */
let $name = document.querySelector("input[data-lang='" + formName.name + "_translate_0_" + $locale + "']");

executeFunc(function charUrlCode()
{
    if($name === null)
    {
        return false;
    }

    if(typeof catUrl.debounce !== 'function')
    {
        return false;
    }


    $name.addEventListener('input', catUrl.debounce(500));

    return true;
})


//if($name)
//{
//
//    let retry = 100;
//
//    setTimeout(function RLTnSEEzgM()
//    {
//
//        if(retry >= 1000)
//        { return; }
//
//        if(typeof catUrl.debounce === 'function')
//        {
//
//            $name.addEventListener('input', catUrl.debounce(500));
//            return;
//        }
//
//        retry = retry * 2;
//
//        setTimeout(RLTnSEEzgM, retry);
//
//    }, 100);
//
//
//    function catUrl()
//    {
//        /* Заполняем транслитом URL */
//        document.getElementById(formName + '_info_url').value = translitRuEn(this.value).toLowerCase();
//    }
//}

function catUrl()
{
    let inputUrl = document.getElementById(formName.name + '_info_url');

    if(inputUrl === null)
    {
        return;
    }

    /* Заполняем транслитом URL */
    inputUrl.value = translitRuEn(this.value).toLowerCase();
}


// Функция для установки поведения чекбоксов торговых предложений
function setupCheckboxGroup(namePart)
{
    const checkboxes = document.querySelectorAll(`input[type="checkbox"][name*="${namePart}"]`);

    if(checkboxes.length > 0)
    {
        checkboxes.forEach(function(item)
        {
            item.addEventListener('change', function()
            {
                const currentCheck = this.id; // ID текущего чекбокса
                checkboxes.forEach(function(event)
                {
                    if(event.id !== currentCheck)
                    {
                        event.checked = false; // Снимаем отметку с других чекбоксов
                    }
                });
            });
        });
    }
}

setupCheckboxGroup('price');
setupCheckboxGroup('article');
setupCheckboxGroup('quantitative');
setupCheckboxGroup('image');


/** Обрабатываем чекбоксы торговых предложений и вариантов */

$checkboxOfferSettings = document.getElementById(formName.name + '_offer_offer');
$checkboxVariationSettings = document.getElementById(formName.name + '_offer_variation_variation');
$checkboxModificationSettings = document.getElementById(formName.name + '_offer_variation_modification_modification');


$nameOfferSettings = document.querySelectorAll("input[id^='" + formName.name + "_offer_translate']");
$nameVariationSettings = document.querySelectorAll("input[id^='" + formName.name + "_offer_variation_translate']");
$nameModificationSettings = document.querySelectorAll("input[id^='" + formName.name + "_offer_variation_modification_translate']");


toggleSettings($checkboxOfferSettings);
toggleSettings($checkboxVariationSettings);
toggleSettings($checkboxModificationSettings);


// Функция для управления видимостью и обязательностью полей
function toggleSettings(checkbox, children = null)
{
    let settingsContainer = document.getElementById(checkbox.id + '_settings');

    let inputElements;

    if(checkbox.id === $checkboxOfferSettings.id)
    {
        inputElements = $nameOfferSettings;
    }

    if(checkbox.id === $checkboxVariationSettings.id)
    {
        inputElements = $nameVariationSettings;
    }

    if(checkbox.id === $checkboxModificationSettings.id)
    {
        inputElements = $nameModificationSettings;
    }

    if(checkbox.checked)
    {

        // показываем блок натсроек
        settingsContainer.classList.remove('d-none');

        /** Сбрасываем обязательные для заполнения элементы */
        Array.from(inputElements).forEach(input =>
        {
            if(!input.name.includes("postfix"))
            {
                input.setAttribute('required', true);
            }
        });

        /** Отключаем конпку выбора дочернего элемента */
        if(children)
        {
            children.disabled = false;
        }

    } else
    {

        // скрываем блок натсроек
        settingsContainer.classList.add('d-none');

        /** Если кнопка Торговое предложение -  отключаем кнопку Множественный вариант */
        if(checkbox.id === formName.name + '_offer_offer')
        {
            $checkboxVariationSettings.checked = false;
            $checkboxVariationSettings.chedisabledcked = true;
            toggleSettings($checkboxVariationSettings);
        }

        /** Если кнопка Множественный вариант - отключаем кнопку Модификаций вариант */
        if(checkbox.id === $checkboxVariationSettings.id)
        {
            $checkboxModificationSettings.checked = false;
            $checkboxModificationSettings.disabled = true;
            toggleSettings($checkboxModificationSettings);
        }

        /** Делаем элементы обязательными для заполнения */
        Array.from(inputElements).forEach(input => input.removeAttribute('required'));

    }
}

// Обработчик для чекбокса "Offer"
$checkboxOfferSettings.addEventListener('change', function()
{
    toggleSettings(this, $checkboxVariationSettings);
});

// Обработчик для чекбокса "Variation"
$checkboxVariationSettings.addEventListener('change', function()
{
    toggleSettings(this, $checkboxModificationSettings);
});

// Обработчик для чекбокса "Modification"
$checkboxModificationSettings.addEventListener('change', function()
{
    toggleSettings(this);
});


//$checkboxOfferSettings.addEventListener('change', function()
//{
//
//    if(this.checked === false)
//    {
//        $offerSettings.classList.add('d-none');
//
//        $checkboxVariationSettings.checked = false;
//        $checkboxVariationSettings.disabled = true;
//
//        $checkboxModificationSettings.checked = false;
//        $checkboxModificationSettings.disabled = true;
//
//
//        $variationSettings.classList.add('d-none');
//
//        $modificationSettings.classList.add('d-none');
//
//        /* Делаем поле Name НЕ обязательным */
//        Array.from($nameOfferSettings).forEach(e => e.removeAttribute('required'));
//        Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
//        Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
//
//
//    } else
//    {
//
//        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//        Array.from($nameOfferSettings).forEach(e =>
//        {
//            if(e.name.match("postfix") === null)
//            {
//                e.setAttribute('required', true)
//            }
//        });
//
//        $offerSettings.classList.remove('d-none');
//
//        $checkboxVariationSettings.disabled = false;
//        $checkboxModificationSettings.disabled = false;
//    }
//});
//
//
//$checkboxVariationSettings.addEventListener('change', function()
//{
//    if(this.checked === false)
//    {
//        $variationSettings.classList.add('d-none');
//
//
//        $checkboxModificationSettings.checked = false;
//        $checkboxModificationSettings.disabled = true;
//
//        $modificationSettings.classList.add('d-none');
//
//
//        /* Делаем поле Name НЕ обязательным */
//        Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
//        Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
//
//    } else
//    {
//        $variationSettings.classList.remove('d-none');
//
//        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//        Array.from($nameVariationSettings).forEach(e =>
//        {
//            if(e.name.match("postfix") === null)
//            {
//                e.setAttribute('required', true)
//            }
//        });
//
//        $checkboxModificationSettings.disabled = false;
//
//    }
//});
//
//
//$checkboxModificationSettings.addEventListener('change', function()
//{
//
//    if(this.checked === false)
//    {
//        $modificationSettings.classList.add('d-none');
//
//        /* Делаем поле Name НЕ обязательным */
//        Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
//
//    } else
//    {
//        $modificationSettings.classList.remove('d-none');
//
//        /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//        Array.from($nameModificationSettings).forEach(e =>
//        {
//            if(e.name.match("postfix") === null)
//            {
//                e.setAttribute('required', true)
//            }
//        });
//
//    }
//});
//

//
//if($checkboxOfferSettings.checked === false)
//{
//    $offerSettings.classList.add('d-none');
//
//    $checkboxVariationSettings.checked = false;
//    $checkboxVariationSettings.disabled = true;
//
//    $checkboxModificationSettings.checked = false;
//    $checkboxModificationSettings.disabled = true;
//
//
//    /* Делаем поле Name НЕ обязательным */
//
//    Array.from($nameOfferSettings).forEach(e => e.removeAttribute('required'));
//
//    Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
//    $variationSettings.classList.add('d-none');
//
//    Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
//    $modificationSettings.classList.add('d-none');
//
//
//} else
//{
//
//    $offerSettings.classList.remove('d-none');
//
//    $checkboxVariationSettings.disabled = false;
//    $checkboxModificationSettings.disabled = false;
//
//    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//    Array.from($nameOfferSettings).forEach(e =>
//    {
//        if(e.name.match("postfix") === null)
//        {
//            e.setAttribute('required', true)
//        }
//    });
//}
//
//
//if($checkboxVariationSettings.checked === false)
//{
//    /* Делаем поле Name НЕ обязательным */
//    Array.from($nameVariationSettings).forEach(e => e.removeAttribute('required'));
//
//    $variationSettings.classList.add('d-none');
//
//} else
//{
//    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//    Array.from($nameVariationSettings).forEach(e =>
//    {
//        if(e.name.match("postfix") === null)
//        {
//            e.setAttribute('required', true)
//        }
//    });
//
//    $variationSettings.classList.remove('d-none');
//}
//
//
//if($checkboxModificationSettings.checked === false)
//{
//    /* Делаем поле Name НЕ обязательным */
//    Array.from($nameModificationSettings).forEach(e => e.removeAttribute('required'));
//
//    $modificationSettings.classList.add('d-none');
//
//} else
//{
//    /* Делаем поле Name ОБЯЗАТЕЛЬНЫМ */
//    Array.from($nameModificationSettings).forEach(e =>
//    {
//        if(e.name.match("postfix") === null)
//        {
//            e.setAttribute('required', true)
//        }
//    });
//
//
//    $modificationSettings.classList.remove('d-none');
//}
//


function chanfeReferenc($this)
{

    let ref = document.getElementById($this.dataset.reference);
    if($this.checked === true)
    {
        ref.classList.remove('d-none');
    } else
    {
        ref.classList.add('d-none');
        ref.selectedIndex = 0; /* сбрасываем select */
    }
}


document.querySelectorAll('.change-postfix').forEach(function(postfix)
{

    postfix.addEventListener('change', function()
    {

        document.querySelectorAll('.' + this.id).forEach(function(element)
        {

            if(postfix.checked === true)
            {
                element.classList.remove('d-none');
                element.querySelector('input').setAttribute('required', true);
            } else
            {
                element.classList.add('d-none');
                let inpt = element.querySelector('input');
                inpt.removeAttribute('required');
                inpt.removeAttribute('value');
            }

        });
    });
});


/** Добавить контактный телефон */

document.querySelectorAll('#category_material_form-add').forEach(function(item)
{
    item.addEventListener('click', addDomain);
});

function addDomain()
{

    /* Получаем прототип формы */
    let newForm = this.dataset.prototype;
    let index = this.dataset.index * 1;
    let collection = this.dataset.collection;

    newForm = newForm.replace(/__category_domain__/g, index)

    let div = document.createElement('div');
    div.innerHTML = newForm;
    div.id = 'item_category_material_form_domain_' + index;
    div.classList.add('mb-3');

    let $collection = document.getElementById(collection);
    $collection.append(div);

    /* Удаляем контактный номер телефона */
    (div.querySelector('.del-item-domain'))?.addEventListener('click', deletePhone);

    this.dataset.index = (index + 1).toString();

}

document.querySelectorAll('.del-item-domain').forEach(function(item)
{
    item.addEventListener('click', deletePhone);
});

function deletePhone()
{
    document.getElementById(this.dataset.delete).remove();
}
