// Constants, helpers
function removeTags(str) {
    if ((str===null) || (str===''))
        return false;
    else
        str = str.toString();
    return str.replace( /(<([^>]+)>)/ig, '');
}

async function loadJson(url) {
  let response = await fetch(url);

  if (response.status == 200) {
    let json = await response.json();
    return json;
  }

  throw new Error(response.status);
}

// activateThemeElement(id, type) {
//   `${type}.list__item[data-state=selected]`
//   if (type === 'themes') {

//   }
// }

/* Todo: Refactor these to functions into one */
function activateThemeElement(id) {
  const current = document.querySelector('#themes .list__item[data-state=selected]')
  if (current.getAttribute('data-theme-id') === id) return false;

  if (current) {
    current.removeAttribute('data-state')
  }
  const element = document.querySelector(`[data-theme-id="${id}"]`)
  element.setAttribute('data-state', 'selected')
  getDataByTheme(id)
  loadJson(`/api/theme/set-last.php?id=${id}`)
}

function activatePresetElement(id) {
  const current = document.querySelector('#presets .list__item[data-state=selected]')
  if (current && current.getAttribute('data-preset-id') === id) {
    return false
  };
  if (current) {
    current.removeAttribute('data-state')
  }
  const element = document.querySelector(`[data-preset-id="${id}"]`)
  element.setAttribute('data-state', 'selected')

  const currentThemeId = document.querySelector('#themes .list__item[data-state=selected]').getAttribute('data-theme-id')

  setVolumeAttributes()
  loadJson(`/api/preset/set-last.php?preset_id=${id}&theme_id=${currentThemeId}`)
}

function setVolumeAttributes() {
  console.log("Moving volume sliders and knobs...")
}

// Application state

// getters & setters
function getDataByTheme(id) {
  // Remove current presets
  // Remove current tracks
  // Remove current effects
  // Get and print all of the above for the new id
  console.log("Get lots of data for id " + id)
}

// DOM Node references
// DOM update functions
// Event handlers

/* Todo: Refactor these two EH-blocks into one */
const themesSection = document.querySelector('#themes')
themesSection.addEventListener('click', (ev) => {
  if (ev.target.id === "add-theme") {
    const button = ev.target
    const input = button.previousElementSibling
    const value = removeTags(input.value)
    const parent = themesSection.querySelector('.list')
    loadJson(`/api/theme/create.php?name=${value}`)
    .then(id => {
      input.value = ""
      const template = document.querySelector('#theme-item')
      const clone = template.content.cloneNode(true)
      const li = clone.querySelector('.themes-list__item')
      li.setAttribute('data-theme-id', id)
      clone.querySelector('.themes-list__item input[type=button]').value = value
      parent.append(clone)
      activateThemeElement(id)
    })
  }
  if (ev.target.getAttribute('data-action') === 'select') {
    const id = ev.target.parentElement.getAttribute('data-theme-id')
    activateThemeElement(id)
  }
  if (ev.target.getAttribute('data-action') === 'delete') {
    const li = ev.target.parentElement
    const id = li.getAttribute('data-theme-id')
    const selected = themesSection.querySelector('[data-state=selected]').getAttribute('data-theme-id')
    li.remove()
    if (id === selected) {
      let available = themesSection.querySelector('.themes-list__item').getAttribute('data-theme-id')
      activateThemeElement(available)
    }
    loadJson(`/api/theme/delete.php?id=${id}`)
  }
})

/* Basically a copy of the previous block - ugh. */
const presetsSection = document.querySelector('#presets')
presetsSection.addEventListener('click', (ev) => {
  if (ev.target.id === "add-preset") {
    const button = ev.target
    const input = button.previousElementSibling
    const value = removeTags(input.value)
    const parent = presetsSection.querySelector('.list')
    const currentThemeId = document.querySelector('#themes .list__item[data-state=selected]').getAttribute('data-theme-id')
    loadJson(`/api/preset/create.php?name=${value}&theme_id=${currentThemeId}`)
    .then(id => {
      input.value = ""
      const template = document.querySelector('#preset-item')
      const clone = template.content.cloneNode(true)
      const li = clone.querySelector('.list__item')
      li.setAttribute('data-preset-id', id)
      clone.querySelector('.list__item input[type=button]').value = value
      parent.append(clone)
      activatePresetElement(id)
    })
  }
  if (ev.target.getAttribute('data-action') === 'select') {
    const id = ev.target.parentElement.getAttribute('data-preset-id')
    activatePresetElement(id)
  }
  if (ev.target.getAttribute('data-action') === 'delete') {
    const li = ev.target.parentElement
    const id = li.getAttribute('data-preset-id')
    const selected = presetsSection.querySelector('[data-state=selected]').getAttribute('data-preset-id')
    li.remove()

    if (id === selected) {
      let available = presetsSection.querySelector('.list__item').getAttribute('data-preset-id')
      activatePresetElement(available)
    }
    loadJson(`/api/preset/delete.php?id=${id}`)
  }
})

// Event handler bindings
// Initial setup