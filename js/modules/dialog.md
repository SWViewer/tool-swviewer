
// createDialog function call is an example of how to create dialog.
//
// createDialog({
//     parentId: 'abc',
//     id: 'xyz',
//     title: 'Presets', //optional
//     removable: true | false, //optional
//     alert: {//optional
//         emoji: '👋',
//         message: 'A line of message',
//     },
//     custom: {//optional
//         insertElement: 'AN html element'
//     },
//     buttons: [//optional | An array of buttons
//         {
//             type: 'positive|negative|accent|secondary-outlined by default',
//             title: 'Save',
//             onClick: method(), //optional
//             remove: true | false //optional (remove dialog after click)
//         },
//         {
//             type: 'negative',
//             title: 'Cancel',
//             onClick: method(), //optional
//             remove: true | false //optional (remove dialog after click)
//         }
//     ]
// });


// To remove the existing dialog.
// 1. Pass id to remove specific dialog.
// 2. If id is not provided it will remove the topmost dialog only when the dialog has set type removable to ture.


// To create the new dialog.