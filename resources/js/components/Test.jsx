export default  function Test() {
    const objs = [
        {
            id: 1,
            name : 'toto'
        },
        {
            id: 2,
            name : 'titit'
        }
    ]
    return <div onClick={() => console.log('hello')}>
        {
            objs.map((obj) => (
                <div key={obj.id}>{obj.name}</div>
            ))
        }
    </div>;
}
