/**
 * 2017-2018 Moip Wirecard Brasil
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author MOIP DEVS - <prestashop@moip.com.br>
 *  @copyright  2017-2018 Moip Wirecard Brasil
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Moip Wirecard Brasil
 */

 (function(window) {
    var _eloBins = ["401178", "401179", "431274", "438935", "451416", "457393", "457631", "457632", "504175", "627780", "636297", "636368"];
    var _eloBinRanges = [
      ["506699", "506778"],
      ["509000", "509999"],
      ["650031", "650033"],
      ["650035", "650051"],
      ["650405", "650439"],
      ["650485", "650538"],
      ["650541", "650598"],
      ["650700", "650718"],
      ["650720", "650727"],
      ["650901", "650920"],
      ["651652", "651679"],
      ["655000", "655019"],
      ["655021", "655058"]
    ];
    var _hiperBins = ["637095", "637612", "637599", "637609", "637568"];
    var _hipercardBins = ["606282", "384100", "384140", "384160"];
    var _masterCardRanges = ["222100", "272099"];
    _isInEloBinRanges = function(bin) {
            var numbin = parseInt(bin);
            for (var i = 0; i < _eloBinRanges.length; i++) {
                var start = _eloBinRanges[i][0], end = _eloBinRanges[i][1];
                if (numbin >= start && numbin <= end) return true;
            }
            return false;
    },
    _isInMasterCardRanges = function(bin) {
      var numRange = parseInt(bin);
        for (var i = 0; i < _masterCardRanges.length; i += 2) {
          var startingRange = _masterCardRanges[i],
              endingRange = _masterCardRanges[i + 1];
          if (numRange >= startingRange && numRange <= endingRange) return true;
        }
        return false;
    },
    normalizeCardNumber = function(creditCardNumber) {
      if (!creditCardNumber) {
        return creditCardNumber;
      }
      creditCardNumber += '';
      return creditCardNumber.replace(/[\s+|\.|\-]/g, '');
    },
    cardType = function(creditCardNumber, loose) {
      var cardNumber = normalizeCardNumber(creditCardNumber);
      var getBin = function(cardNum) {
        return cardNum.substring(0, 6);
      };
      brands = {
          VISA: {
            matches: function(cardNum) {
              return /^4\d{3}\d*$/.test(cardNum);
            }
          },
          MASTERCARD: {
            matches: function(cardNum) {
              return /^5[1-5]\d{4}\d*$/.test(cardNum) || (cardNum !== null && cardNum.length == 16 && _isInMasterCardRanges(getBin(cardNum)));
            }
          },
          AMEX: {
            matches: function(cardNum) {
              return /^3[4,7]\d{2}\d*$/.test(cardNum);
            }
          },
          DINERS: {
            matches: function(cardNum) {
              return /^3(?:0[0-5]|[68][0-9])+\d*$/.test(cardNum);
            }
          },
          HIPERCARD: {
            matches: function(cardNum) {
              return cardNum !== null && cardNum.length >= 6 && _hipercardBins.indexOf(getBin(cardNum)) > -1;
            }
          },
          ELO: {
            matches: function(cardNum) {
              return cardNum !== null && cardNum.length >= 6 && (_eloBins.indexOf(getBin(cardNum)) > -1 || _isInEloBinRanges(getBin(cardNum)));
            }
          },
          HIPER: {
            matches: function(cardNum) {
              return cardNum !== null && cardNum.length >= 6 && _hiperBins.indexOf(getBin(cardNum)) > -1;
            }
          }
      }
      if (brands.ELO.matches(cardNumber)) {
        return {
          brand: 'ELO'
        };
      }
      if (brands.HIPER.matches(cardNumber)) {
        return {
          brand: 'HIPER'
        };
      }
      if (brands.VISA.matches(cardNumber)) {
        return {
          brand: 'VISA'
        };
      }
      if (brands.MASTERCARD.matches(cardNumber)) {
        return {
          brand: 'MASTERCARD'
        };
      }
      if (brands.AMEX.matches(cardNumber)) {
        return {
          brand: 'AMEX'
        };
      }
      if (brands.HIPERCARD.matches(cardNumber)) {
        return {
          brand: 'HIPERCARD'
        };
      }
      if (brands.DINERS.matches(cardNumber)) {
        return {
          brand: 'DINERS'
        };
      }
      return null;
    }


})(window);