		<article id="order" class="mainarticles bottom_border_blue">
            <div class="blue_line mainarticleheaders line_header"><h1>Bestellformular</h1></div>
            <form>
                St�ckzahl: <input name="order_amount" type="number"  min="1" max="20" value="1" title="St�ckzahl"><br>
                <br>
                Lieferadresse: <br>
                Name: <input type="text" name="order_name" size="20" placeholder="Mustermann" required> Vorname: <input type="text" name="order_firstname" size="20" placeholder="Max" required><br>
                Stra�e: <input type="text" name="order_street" size="20" placeholder="Stra�e" required> Hausnummer: <input type="text" name="order_housenumber" size="3" required><br>
                Postleitzahl: <input type="text" name="order_plz" size="20" placeholder="PLZ" required> Ort: <input type="text" name="order_city" size="20" placeholder="Ort" required> <br> <br>

                E-Mail: <input type="email" name="order_email" size="20" placeholder="mustermann.max@beispiel.de" required>  <br><br>

                Zahlungsmethode: <br>
                <input type="radio" name="order_paymethod" value="paypal" checked> PayPal <br>
                <input type="radio" name="order_paymethod" value="vorkasse"> Vorkasse <br><br>
                <input type="submit" title="Zur Kasse" value="Zur Kasse">
            </form>
        </article>