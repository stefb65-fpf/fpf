import React from 'react';
import ReactDOM from 'react-dom';
import React, {Component} from "react";

class App extends Component {
    render() {
        return (
            <h1>Hello React!</h1>
        );
    }
}

if (document.getElementById('hello-react')) {
    ReactDOM.render(<App/>, document.getElementById('hello-react'));
}
export default App
