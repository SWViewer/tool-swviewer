// function getMessage(event) {
//     if (event.origin !== 'https://swviewer.toolforge.org') return;
//     if (!event.data.lang || !event.data.orient || !event.data.messages) return;

//     var els = document.getElementsByClassName("custom-lang");
//     console.log(els)

//     document.getElementById("parentHTML").setAttribute("dir", event.data.orient);
//     document.getElementById("parentHTML").setAttribute("lang", event.data.lang);

//     for (el in els) {
//         console.log(el)
//         if (typeof els[el].textContent !== "undefined" && els[el].textContent !== 0 && els[el].textContent !== null)
//             if (event.data.messages.hasOwnProperty(els[el].textContent.replace("[","").replace("]", "")))
//                  els[el].textContent = event.data.messages[els[el].textContent.replace("[","").replace("]", "")];
//      }
//      window.parent.document.getElementById("page").style.display = "block";
// };
// window.addEventListener("message", getMessage, false);