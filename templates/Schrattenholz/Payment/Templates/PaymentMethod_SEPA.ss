<div id="IBAN_Holder" class="field text col-sm-6 form-group" wfd-id="109">
	<label class="left" for="IBAN" wfd-id="111">IBAN</label>
	<div class="middleColumn" wfd-id="110">
		<input id="IBAN" name="IBAN" class="form-control" type="text" wfd-id="197" <% if $IBAN_Hint %>value="$IBAN_Hint" required<% end_if %> attr-required="true">
	</div>
</div>
<div id="BIC_Holder" class="field text col-sm-6 form-group" wfd-id="109">
	<label class="left" for="BIC" wfd-id="111">BIC</label>
	<div class="middleColumn" wfd-id="110">
		<input id="BIC" name="BIC" class="form-control" type="text" wfd-id="197" <% if $BIC_Hint %>value="$BIC_Hint" required<% end_if %> attr-required="true">
	</div>
</div>
<% if $CurrentMember %>
<div id="SAVE_SEPA_Holder" class="field text  form-group" wfd-id="109">
	<div class="custom-control custom-checkbox">
		<input class="custom-control-input" type="checkbox" id="SAVE_SEPA" name="SAVE_SEPA" <% if $SAVE_SEPA %>checked="checked"<% end_if %>>
		<label class="custom-control-label" for="SAVE_SEPA">Diese Kontoverbindung f&uumlr zuk&uuml;nftige Eink&auml;ufe in meinem Benutzerkonto hinterlegen.
	</div>
</div>
<% end_if %>
<div class="form-group">
	<div class="custom-control custom-checkbox">
		<input class="custom-control-input" type="checkbox" id="SEPA_Confirmation" attr-required="true" name="SEPA_Confirmation" required attr-required="true">
		<label class="custom-control-label" for="SEPA_Confirmation">Ich stimme zu, dass der Biolandhof Sehnenm&uuml;hle den Rechnungsbetrag per Lastschriftmandat von meinem Konto einzieht.
	</div>
</div>
