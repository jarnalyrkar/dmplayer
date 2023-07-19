// Constants, helpers
const keystrokes = '123456789abcdefghijklmnopqrstuvwxyz'.split('')
let theme_width = document.querySelector('#theme').clientWidth // inline-padding
document.querySelector('#theme').style.width = theme_width + "px"
setListHeight();

function setListHeight() {

  // get header height
  const header = document.querySelector('header')
  const headerHeight = header.clientHeight
  // get footer height
  const footerHeight = document.querySelector('footer').clientHeight
  const margin = parseFloat(window.getComputedStyle(header)['marginBottom']) +
                parseFloat(window.getComputedStyle(header)['marginTop'])
  const taken = headerHeight + footerHeight + margin
  document.querySelectorAll('.list').forEach(list => {
    list.style.maxHeight = `calc(100vh - ${taken}px)`
  })

}

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

function randomBetween(min, max) { // min and max included
  return Math.floor(Math.random() * (max - min + 1) + min)
}

function activateElement(id, list) {
  const current = list.querySelector('.list__item[data-state=selected]')
  if (current && current.getAttribute('data-id') === id) return false;

  if (current) {
    current.removeAttribute('data-state')
  }
  const element = list.querySelector(`[data-id="${id}"]`)
  element.setAttribute('data-state', 'selected')

  if (list.parentElement.id === "theme") {
    getDataByTheme(id)
    loadJson(`/api/theme/set-last.php?id=${id}`)
  }
  if (list.parentElement.id === "preset") {
    const currentThemeId = document.querySelector('#theme .list__item[data-state=selected]').getAttribute('data-id')
    loadJson(`/api/preset/set-last.php?preset_id=${id}&theme_id=${currentThemeId}`)
  }
}

function setVolumeAttributes() {
  console.log("Moving volume sliders and knobs...")
}

function fadeOut(audio) {
  const steps = 0.025
  const interval = 50
  let fadeout = setInterval(() => {
    if (audio.volume >= steps) {
      try {
        audio.volume -= steps
      } catch (error) {
        audio.remove()
        clearInterval(fadeout)
        return
      }
    } else {
      audio.pause()
      return
    }
  }, interval)
}

function fadeIn(audio, targetVolume) {
  audio.volume = 0
  audio.play()
  const steps = 0.025
  const interval = 50
  let fadein = setInterval(() => {
    if (audio.volume.toFixed(2) <= (targetVolume / 100) - steps) {
      audio.volume += steps
    } else {
      audio.volume = targetVolume / 100
      clearInterval(fadein)
    }
  }, interval)
}

function fadeTo(audio, targetVolume) {
  const steps = 0.025
  const target = targetVolume / 100
  const interval = 50
  const audio_id = audio.getAttribute('data-id')
  const track = document.querySelector(`li[data-id="${audio_id}"]`)
  if (track) {
    let range = track.querySelector('input[type=range]')
    animateRange(range, targetVolume)
  }
  if (audio.volume < target) {
    let fadeTo = setInterval(() => {
      if (audio.volume.toFixed(2) >= target - steps) {
        audio.volume = target
        clearInterval(fadeTo)
      } else {
        audio.volume += steps
      }
    }, interval)
  } else {
    let fadeTo = setInterval(() => {
      if (audio.volume.toFixed(2) <= target + steps) {
        audio.volume = target
        clearInterval(fadeTo)
      } else {
        audio.volume -= steps
      }
    }, interval)
  }
}

function animateRange(range, value) {
  if (range.value > value) {
     let move = setInterval(() => {
       if (range.value > value) {
         range.value--
       } else {
         clearInterval(move)
       }
     }, 50)
  } else {
    let move = setInterval(() => {
       if (range.value < value) {
         range.value++
       } else {
         clearInterval(move)
       }
     }, 50)
  }
}

function playEffect(id) {
  createAudio(id).then(audio => {
    if (audio) {
      audio.play()
      audio.addEventListener('ended', () => {
        audio.remove()
      })
    }
  })
}

// Application state

// getters & setters
function getDataByTheme(id) {
  setPresets(id)
  setTracks(id)
  setEffects(id)
}

function setPresets(theme_id) {
  const parent = document.querySelector('#preset .list')
  parent.innerHTML = ""
  loadJson(`/api/preset/get.php?id=${theme_id}`).then(data => {
    if (data.length > 0) {
      data.forEach(item => {
        const template = document.querySelector('#item')
        const clone = template.content.cloneNode(true)
        const li = clone.querySelector('.list__item')
        li.setAttribute('data-id', item.preset_id)
        if (item.current) li.setAttribute('data-state', 'selected')
        clone.querySelector('[data-action=select]').value = item.name
        parent.appendChild(clone)
      })
    }
  })
}

function setTracks(theme_id) {
  const parent = document.querySelector('#track .list')
  parent.innerHTML = ""
  loadJson(`/api/track/get.php?theme_id=${theme_id}&type=1`).then(data => {
    if (data && data.length > 0) {
      data.forEach(item => {
        const template = document.querySelector("#track-item")
        const clone = template.content.cloneNode(true)
        const li = clone.querySelector('li')
        const preset_id = document.querySelector('#preset [data-state=selected]').getAttribute('data-id')
        li.setAttribute('data-id', item.track_id)
        li.querySelector('[data-action=play]').classList.add('active')
        loadJson(`/api/preset/track-settings.php?preset_id=${preset_id}&track_id=${item.track_id}`).then(data => {
          const range = li.querySelector('input[type=range]')
          if (range) {
            animateRange(range, data.volume)
          }
          if (data.playing) {
            const existing = document.querySelector(`audio[data-id="${track.getAttribute('data-id')}"]`)
            if (!existing) {
              createAudio(item.track_id).then(audio => {
                if (audio) {
                  if (audio.paused) {
                    audio.play()
                  }
                  fadeTo(audio, data.volume)
                }
              })
            } else {
              if (existing.paused) {
                existing.play()
              }
              fadeTo(existing, data.volume)
            }
          }

        })
        li.querySelector('.track-title').innerHTML = item.name
        parent.appendChild(clone)
      })
    } else {
      parent.innerHTML = '<li class="empty">No tracks added yet!</li>'
    }
  })
}

function setEffects(theme_id) {
  const parent = document.querySelector('#effect .list')
  parent.innerHTML = ""
  loadJson(`/api/track/get.php?theme_id=${theme_id}&type=2`).then(data => {
    if (data && data.length > 0) {
      let counter = 0
      data.forEach(item => {
        const template = document.querySelector("#effect-item")
        const clone = template.content.cloneNode(true)
        const li = clone.querySelector('li')
        const keystroke = keystrokes[parent.querySelectorAll('li:not(.empty)').length + counter]
        li.setAttribute('data-id', item.track_id)
        li.querySelector('.track-title').innerHTML = item.name
        li.setAttribute('data-keystroke', keystroke)
        li.querySelector('.keystroke').innerHTML = keystroke
        parent.appendChild(clone)
        counter++;
      })
    } else {
      parent.innerHTML = '<li class="empty">No tracks added yet!</li>'
    }
  })
}

// DOM Node references
// DOM update functions
function createTheme(value, list) {
  loadJson(`/api/theme/create.php?name=${value}`)
  .then(id => {
    const template = document.querySelector('#item')
    const clone = template.content.cloneNode(true)
    const li = clone.querySelector('.list__item')
    li.setAttribute('data-id', id)
    clone.querySelector('.list__item input[type=button]').value = value
    list.append(clone)
    activateElement(id, list)
    const empty = list.querySelector('.empty')
    if (empty) empty.remove()
    // Add default preset
    const presetList = document.querySelector('#preset .list')
    createPreset('Default', id, presetList)
  })
}

function createPreset(value, theme_id, list) {
  let current = 0
  if (value === "Default") {
    current = 1
  }
  loadJson(`/api/preset/create.php?name=${value}&theme_id=${theme_id}&current=${current}`)
  .then(id => {
    const template = document.querySelector('#item')
    const clone = template.content.cloneNode(true)
    const li = clone.querySelector('.list__item')
    li.setAttribute('data-id', id)
    clone.querySelector('.list__item input[type=button]').value = value
    list.append(clone)
    if (current) {
      loadJson(`/api/preset/set-last.php?preset_id=${id}&theme_id=${theme_id}`)
    }
    activateElement(id, list)
    const empty = list.querySelector(".empty")
    if (empty) empty.remove()
  })
}

function createTrack(value, theme_id, list) {
    loadJson(`/api/track/create.php?name=${value}&theme_id=${theme_id}&type_id=1`)
    .then(id => {
      const template = document.querySelector('#track-item')
      const clone = template.content.cloneNode(true)
      const li = clone.querySelector('li')
      li.setAttribute('data-id', id)
      li.querySelector('.track-title').innerHTML = value
      list.append(clone)
      // TODO: Add exclamation for "Add file to this track"
      const empty = list.querySelector('.empty')
      if (empty) empty.remove()
    })
}

function createEffect(value, theme_id, list) {
  loadJson(`/api/track/create.php?name=${value}&theme_id=${theme_id}&type_id=2`)
  .then(id => {
    const template = document.querySelector('#effect-item')
    const clone = template.content.cloneNode(true)
    const li = clone.querySelector('li')
    li.setAttribute('data-id', id)
    li.querySelector('.track-title').innerHTML = value
    list.append(clone)
    const keystroke = keystrokes[list.querySelectorAll('li:not(.empty)').length - 1]
    li.setAttribute('data-keystroke', keystroke)
    li.querySelector('.keystroke').innerHTML = keystroke
    // TODO: Add exclamation for "Add file to this track"
    const empty = list.querySelector('.empty')
    if (empty) empty.remove()
  })
}

async function createAudio(id) {
  if (document.querySelector(`audio[data-id="${id}"]`)) return;
  const el = document.createElement('audio')
  const path = await loadJson('/api/file/get_path.php')
  const audio = await loadJson(`/api/file/random.php?id=${id}`)
  if (audio && path) {
    el.src = path + audio.filename
    el.setAttribute('data-id', id)
    document.body.appendChild(el)
    // get and play new track when ended
    el.addEventListener('ended', () => {
      el.remove()
      const timeout = randomBetween(3000, 15000)
      setTimeout(() => {
        createAudio(id).then(newEl => newEl.play())
      }, timeout)
    })
    return el
  } else {
    return false
  }
}

function removePresets() {
  const presetList = document.querySelector('#preset .list')
  presetList.querySelectorAll('li').forEach(preset => preset.remove())
  presetList.innerHTML = '<li class="empty">No presets added yet!</li>'
}

// Event handlers
document.addEventListener('click', (ev) => {

  if (ev.target.getAttribute('data-action') === 'select') {
    const preset_id = ev.target.parentElement.getAttribute('data-id')
    const section = ev.target.closest("section")
    const list = section.querySelector('.list')
    activateElement(preset_id, list)

    if (section.id === "preset") {
      const tracks = document.querySelectorAll('#track .list li')
      tracks.forEach(track => {
        loadJson(`/api/preset/track-settings.php?preset_id=${preset_id}&track_id=${track.getAttribute('data-id')}`).then(data => {

          animateRange(track.querySelector('input[type="range"]'), data.volume)
          if (data.playing) {
            const existing = document.querySelector(`audio[data-id="${track.getAttribute('data-id')}"]`)
            if (!existing) {
              createAudio(track.getAttribute('data-id')).then(audio => {
                if (!audio.paused && audio.duration > 0) {
                  fadeTo(audio, data.volume)
                } else {
                  audio.play()
                }
              })
            } else {
              existing.play()
              fadeTo(existing, data.volume)
            }
          }
        })
      })
    }
  }

  if (ev.target.getAttribute('data-action') === 'delete') {
    const parent = ev.target.closest("section")
    const li = ev.target.closest('li')
    const type = parent.id
    const list = parent.querySelector(".list")
    const id = li.getAttribute('data-id')
    if (type === "effect" || type === "track") {
      loadJson(`/api/track/delete.php?id=${id}`)
      li.remove()
      return
    }
    const selected = list.querySelector('[data-state=selected]')
    if (type === 'theme') {
      loadJson(`/api/preset/delete-related.php?id=${id}`)
    }
    li.remove()
    if (selected && id === selected.getAttribute('data-id')) {
      let available = list.querySelector('.list__item')
      if (!available) {
        list.innerHTML = `<li class="empty">No ${type}s added yet!</li>`
        loadJson(`/api/${type}/delete.php?id=${id}`)
        if (type === "theme") {
          const tracks = document.querySelectorAll('.list li:not(.empty)')
          tracks.forEach(li => li.remove())
        }
        removePresets()
        return
      }
      activateElement(available.getAttribute('data-id'), list)
    } else if(list.children.length === 0) {
      list.innerHTML = `<li class="empty">No ${type}s added yet!</li>`
    }
    loadJson(`/api/${type}/delete.php?id=${id}`)
  }

  if (ev.target.getAttribute('data-action') === 'see-files') {
    const track_id = ev.target.closest('li').getAttribute('data-id')
    const theme_id = document.querySelector('#theme [data-state=selected]').getAttribute('data-id')

    const template = document.querySelector('#track-files')
    const clone = template.content.cloneNode(true)
    const dialog = clone.querySelector(".dialog")

    // Get all existing files for the track
    loadJson(`/api/file/get.php?track_id=${track_id}`).then(files => {
      files.forEach(file => {
        const template = dialog.querySelector('#file')
        const clone = template.content.cloneNode(true)
        const li = clone.querySelector('li')
        li.setAttribute('data-id', file.file_id)
        li.setAttribute('data-filename', file.filename)
        li.querySelector('.file__name').innerHTML = file.filename
        dialog.querySelector('.files').appendChild(clone)
      })
    })

    // listen for uploads
    dialog.querySelector('input[type=file]').addEventListener('change', (ev) => {
      const file = document.querySelector('#new-file').files[0]
      const data = new FormData()
      data.append('file', file)
      data.append('track_id', track_id)
      fetch('/api/file/create.php', {
        method: 'POST',
        body: data
      })
      .then(data => {
        if (data.status === 200) {
          data.json().then(id => {
            const template = document.querySelector('#file')
            const clone = template.content.cloneNode(true)
            const li = clone.querySelector('li')
            li.setAttribute('data-filename', file.name)
            li.setAttribute('data-id', id)
            li.querySelector('.file__name').innerHTML = file.name
            document.querySelector('.files').appendChild(clone)
          })
        }
      })
    })

    document.body.appendChild(clone)
    dialog.classList.add('dialog--show')
  }

  if (ev.target.getAttribute('data-action') === 'play') {
    // find if audio-element already exists with this file_id, if so, fade pause
    const li = ev.target.closest('li')
    const id = li.getAttribute('data-id')
    const existingAudio = document.querySelector(`audio[data-id="${id}"]`)
    const volumeBar = li.querySelector('input[type=range]')
    ev.target.classList.toggle('active')
    let targetVolume = null
    if (volumeBar) {
      targetVolume = volumeBar.value
      if (existingAudio) {
        if (existingAudio.paused) {
          fadeIn(existingAudio, targetVolume)
        } else {
          fadeOut(existingAudio)
        }
        return
      } else {
        createAudio(id).then(audio => {
          if (audio) {
            audio.volume = 0
            audio.play()
            fadeIn(audio, targetVolume)
            return
          }
        })
    }
  } else { // Effect
    createAudio(id).then(audio => {
      if (audio) {
        audio.setAttribute("data-type", "effect")
        audio.volume = document.querySelector('[id=main-effects-volume]').value / 100
        audio.play()
        audio.addEventListener('ended', ev => {
          audio.remove()
        })
      } else {
        // TODO: No file set / No file found
      }
    })
  }
  }

  if (ev.target.getAttribute('data-action') === 'delete-file') {
    const file_id = ev.target.parentElement.getAttribute('data-id')
    loadJson(`/api/file/delete.php?id=${file_id}`)
    ev.target.parentElement.remove()
  }

  if (ev.target.getAttribute('data-action') === 'close-dialog') {
    document.querySelector('.dialog').remove()
  }

  if (ev.target.getAttribute('data-action') === 'stop') {
    const activeFiles = document.querySelectorAll('audio')
    if (activeFiles.length > 0) {
      activeFiles.forEach(file => {
        fadeOut(file)
      })
    }
  }

  if (ev.target.getAttribute('data-action') === 'toggle-themes') {
    const theme = document.querySelector('#theme')
    let transform = theme_width + 32 // inline padding + gap
    if (theme.style.width === "0px") {
      ev.target.querySelector('.arrow').style.transform = "rotateY(0)"
      ev.target.style.backgroundColor = "#402512"
      theme.style.width = theme_width + "px"
      theme.style.transform = `translateX(0px)`
      theme.classList.remove('shrinking')
    } else {
      ev.target.style.backgroundColor = "goldenrod"
      ev.target.querySelector('.arrow').style.transform = "rotateY(180deg)"
      theme.classList.add('shrinking')
      theme.style.width = "0px"
      theme.style.transform = `translateX(-${theme_width}px)`
    }
  }

  if (ev.target.classList.contains('dialog__outer')) {
    document.querySelector('.dialog').remove()
  }
})

document.querySelectorAll('.add-form').forEach(form =>
  form.addEventListener('submit', (ev) => {
    ev.preventDefault()
    const button = form.querySelector('input[type=submit]')
    const input = button.previousElementSibling
    if (input.value === "") {
      form.reportValidity()
    }
    const value = removeTags(input.value)
    const list = button.closest("section").querySelector('.list')
    const type = list.parentElement.id
    let theme_id = ""
    const selectedTheme = document.querySelector('#theme .list__item[data-state=selected]')
    if (selectedTheme) {
      theme_id = selectedTheme.getAttribute('data-id')
    }
    if (type === "theme") {
      createTheme(value, list)
    }
    if (type === "preset") {
      // TODO: If no theme is selected, show "create theme first", message
      createPreset(value, theme_id, list)
    }
    if (type === "track") {
      createTrack(value, theme_id, list)
    }
    if (type === "effect") {
      createEffect(value, theme_id, list)
    }
    input.value = ""
  }
))

document.addEventListener('keydown', ev => {
  const effects = document.querySelectorAll('#effect .list li:not(.empty)')
  const effectsLength = effects.length
  if (effectsLength > 0) {
    for (let i = 0; i < effectsLength; i++) {
      if (ev.code === `Digit${i+1}` || ev.code === `Numpad${i+1}`) {
        const id = effects[i].getAttribute('data-id')
        playEffect(id)
      }
      if (ev.code === "Key" + effects[i].getAttribute('data-keystroke').toLocaleUpperCase()) {
        const id = effects[i].getAttribute('data-id')
        playEffect(id)
      }
    }
  }

  if (ev.code === "Escape") {
    const dialog = document.querySelector('.dialog')
    if (dialog) dialog.remove()
  }
})

// Volume handling
document.addEventListener('change', ev => {
  if (ev.target.getAttribute('data-type') === "music") {
    const audioId = ev.target.closest('li').getAttribute('data-id')
    const audioElement = document.querySelector(`audio[data-id="${audioId}"]`)
    if (audioElement) {
      fadeTo(audioElement, ev.target.value)
    }
  }
  if (ev.target.id === "main-effects-volume") {
    document.querySelectorAll('audio[data-type="effect"]').forEach(effect => {
      fadeTo(effect, ev.target.value)
    })
  }
})

const host = 'ws://127.0.0.1:8009/websockets.php'
const socket = new WebSocket(host)

// Event handler bindings
// Initial setup