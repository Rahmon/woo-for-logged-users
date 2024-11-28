import { render } from '@wordpress/element';
 
function MyFirstApp() {
    return <span>Hello from JavaScript!</span>;
}
 
window.addEventListener(
    'load',
    function () {
        render(
            <MyFirstApp />,
            document.querySelector( '#my-first-gutenberg-app' )
        );
    },
    false
);