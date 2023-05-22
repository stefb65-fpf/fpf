export default  function Toto() {
    // TODO: Actually implement a navigation bar
    const objs = [
        {
            id: 1,
            name : 'tatatatatatata'
        },
        {
            id: 2,
            name : 'defgsdqjgkdwjkfgkdfgk'
        }
    ]
    return <div>
        {
            objs.map((obj) => (
                <div key={obj.id}>{obj.name}</div>
            ))
        }
    </div>;
}
