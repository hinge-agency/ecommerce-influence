document.querySelector('html').addEventListener('optimonk#ready', function () {
    var adapter = OptiMonk.Visitor.createAdapter();
    adapter.Cart.clear();
    {{set_adapter}}
});