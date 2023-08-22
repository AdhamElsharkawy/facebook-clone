window._ = require("lodash");
try {
    window.$ = window.jQuery = require("jquery");
} catch (e) {
    console.log(e);
}

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;

// import Echo from "laravel-echo";


// window.Echo = new Echo({
//     broadcaster: "socket.io",
//     host: window.location.hostname + ":" + window.laravel_echo_port,
// });

// // public channel
// window.Echo.channel("notifications-channel").listen(
//     ".NotificationEvent",
//     (data) => {
//         console.log(data);
//     }
// );

// private channel
// window.Echo.private("user." + window.Laravel.user).listen(
//     ".NotificationEvent",
//     (e) => {
//         console.log(e);
//     }
// );
