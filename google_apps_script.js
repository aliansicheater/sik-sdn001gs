function doPost(e) {
  var output = {};
  try {
    var raw = e.postData.contents;
    var payload = JSON.parse(raw);
    var action = payload.action;

    // Ganti dengan ID Spreadsheet Anda jika diperlukan, atau gunakan getActiveSpreadsheet
    var ss = SpreadsheetApp.getActiveSpreadsheet();

    if (action === 'getData') {
      var sheetName = payload.sheet;
      var sheet = ss.getSheetByName(sheetName);
      if (!sheet) throw new Error("Sheet " + sheetName + " tidak ditemukan");

      var data = sheet.getDataRange().getValues();
      var headers = data[0];
      var result = [];

      for (var i = 1; i < data.length; i++) {
        var rowObj = {};
        for (var j = 0; j < headers.length; j++) {
          rowObj[headers[j]] = data[i][j];
        }

        var match = true;
        if (payload.filters) {
          for (var k in payload.filters) {
            if (payload.filters[k] && rowObj[k] != payload.filters[k]) {
              match = false;
              break;
            }
          }
        }
        if (match) result.push(rowObj);
      }
      output.data = result;
      output.success = true;

    } else if (action === 'addRow') {
      var sheetName = payload.sheet;
      var sheet = ss.getSheetByName(sheetName);
      var data = payload.data;
      var headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];

      var newRow = [];
      for (var i = 0; i < headers.length; i++) {
        newRow.push(data[headers[i]] || "");
      }
      sheet.appendRow(newRow);
      output.success = true;

    } else if (action === 'updateRow') {
      var sheetName = payload.sheet;
      var sheet = ss.getSheetByName(sheetName);
      var rowId = payload.rowId;
      var data = payload.data;

      var range = sheet.getDataRange();
      var values = range.getValues();
      var headers = values[0];
      var idIndex = headers.indexOf('id');

      var updated = false;
      for (var i = 1; i < values.length; i++) {
        if (values[i][idIndex] == rowId) {
          for (var j = 0; j < headers.length; j++) {
            if (data.hasOwnProperty(headers[j])) {
              sheet.getRange(i + 1, j + 1).setValue(data[headers[j]]);
            }
          }
          updated = true;
          break;
        }
      }
      output.success = updated;

    } else if (action === 'deleteRow') {
      var sheetName = payload.sheet;
      var sheet = ss.getSheetByName(sheetName);
      var rowId = payload.rowId;

      var range = sheet.getDataRange();
      var values = range.getValues();
      var headers = values[0];
      var idIndex = headers.indexOf('id');

      var deleted = false;
      for (var i = 1; i < values.length; i++) {
        if (values[i][idIndex] == rowId) {
          sheet.deleteRow(i + 1);
          deleted = true;
          break;
        }
      }
      output.success = deleted;

    } else if (action === 'getUser') {
      var sheet = ss.getSheetByName('Users');
      if (!sheet) throw new Error("Sheet Users tidak ditemukan");
      var username = payload.username;
      var data = sheet.getDataRange().getValues();
      var headers = data[0];

      for (var i = 1; i < data.length; i++) {
        var rowObj = {};
        for (var j = 0; j < headers.length; j++) {
          rowObj[headers[j]] = data[i][j];
        }
        if (rowObj.username === username) {
          output.data = rowObj;
          output.success = true;
          break;
        }
      }
    } else {
      throw new Error("Action tidak dikenali: " + action);
    }
  } catch (error) {
    output.error = error.toString();
    output.success = false;
  }

  return ContentService.createTextOutput(JSON.stringify(output)).setMimeType(ContentService.MimeType.JSON);
}
