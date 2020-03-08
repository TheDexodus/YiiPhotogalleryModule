var lastModelId;

function showDeleteForm (modelId) {
  lastModelId = modelId;
  document.getElementById('form-to-move').action = '/photo/admin/category/delete?id=' + modelId;

  let options = document.querySelectorAll('#select-category>option');
  options.forEach(function(optionElement) {
    if (optionElement.value == modelId) {
      optionElement.setAttribute('disabled', 'disabled');
    }
  });

  document.getElementById('form-to-move').classList.add('active-form');
  document.getElementById('fade-element').classList.remove('fade');
}

function hideDeleteForm () {
  let options = document.querySelectorAll('#select-category>option');
  options.forEach(function(optionElement) {
    if (optionElement.value == lastModelId) {
      optionElement.removeAttribute('disabled');
    }
  });

  document.getElementById('form-to-move').classList.remove('active-form');
  document.getElementById('fade-element').classList.add('fade');
}

function selectDeleteMethod (element) {
  if (element.value == 'move-images') {
    document.getElementById('select-category').classList.remove('fade');
  } else {
    document.getElementById('select-category').classList.add('fade');
  }
}