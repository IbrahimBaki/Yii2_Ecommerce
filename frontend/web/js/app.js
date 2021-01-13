

$(function(){
    const $cartQuantity = $('#cart-quantity');
    const $addToCart = $('.btn-add-to-cart');
    const $itemQuantity = $('.item-quantity');
    $addToCart.click(ev =>{
        ev.preventDefault();
        const $this = $(ev.target);
       const id = $this.closest('.product-item').data('key');
       console.log(id);
       $.ajax({
           method:'POST',
           url:$this.attr('href'),
           data:{id},
           success:function (){
               console.log(arguments)
               $cartQuantity.text(parseInt($cartQuantity.text()|| 0) + 1)
           }
       })
    })

    $itemQuantity.change(ev=>{
        const $this = $(ev.target);
        let $tr = $this.closest('tr');
        let $td = $this.closest('td');
        const id = $tr.data('id');
        $.ajax({
            method:'POST',
            url:$tr.data('url'),
            data:{id, quantity:$this.val()},
            success:function(result){
                $cartQuantity.text(result.quantity);
                $td.next().text(result.price);
                const text = parseFloat($td.next().text().replace('$',''));
                debugger;
            }

        })
    })
});