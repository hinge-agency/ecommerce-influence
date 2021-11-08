document.querySelector('html').addEventListener('optimonk#ready', function () {
    var adapter = OptiMonk.Visitor.createAdapter();
    {{set_adapter}}
});