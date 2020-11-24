window.onload = () => {
    // variables 
    let stripe = Stripe('pk_test_51HMweMJjakv6WiwjSET8nYV0tCraErD3MJ3btGLcymprm3dUVJZ8PAliOI7m4Qeqt7PzTlhWr7u5dwg61JBZzk9R006jkVvYVT')
    let elements = stripe.elements()

    // objets de la page
    let cardHolderName = document.getElementById("name")
    let holderEmail = document.getElementById("email")
    let cardButton = document.getElementById("payment-button")
    let clientSecret = cardButton.dataset.secret;

    // crée les élements de form de la cb
    let card = elements.create("card")
    card.mount("#card-elements")

    // on gère la saisie
    card.addEventListener("change", (event) => {
        let displayError = document.getElementById("card-errors")
        if(event.error)
        {
            displayError.textContent = event.error.message;
        }
        else
        {
            displayError.textContent = "";
        }
    })

    // on gère le paiement et on envoie les infos a stripe
    cardButton.addEventListener("click",() =>{
        stripe.handleCardPayment(
            clientSecret, card, {
                payment_method_data: {
                    billing_details: {
                        name: cardHolderName.value,
                        email: holderEmail.value
                    }
                }
            }
        ).then((result) => {
            if(result.error){
                document.getElementById("errors").innerText = result.error.message
            }
        })
    })
}
