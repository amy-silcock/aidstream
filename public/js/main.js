$(document).ready(function () {
  /* Convert laravel form builder's form prototype to custom form template */
  if ($('.collection-container').length > 0) {
    var proto = $('.collection-container').attr('data-prototype');
    var result = proto.replace(
      /"([\w]+)\[__NAME__\][\w\[\]]+"/g,
      function () {
        var value = arguments[0];
        var length = value.search('[__NAME__]');
        for (var i = 0; i < length; i++) {
          value = value.replace('[__NAME__]', '[__NAME' + i + '__]');
        }
        return value;
      }
    );
    $('.collection-container').removeAttr('data-prototype').append(result);
  }

  /* Add form on click to Add More button */
  $('form').delegate('.add_to_collection', 'click', function () {
    var collection = $(this).attr('data-collection');
    var container = $(this).prev('.collection_form');
    var parents = $(this).parents('.collection_form');
    var level = parents.length;
    var indexString = $(' > .form-group:last-child .form-control', container).eq(0).attr('name');
    if (indexString === undefined) {
      indexString = '';
    }
    var matchedIndexes = indexString.match(/[\d]+/g);
    var parentIndexes = [];
    var newIndex = 0;
    if (matchedIndexes) {
      parentIndexes = matchedIndexes.map(function (i) {
        return parseInt(i);
      });
      newIndex = parentIndexes[level] + 1;
    }
    var protoHtml = level === 0 ? $('.collection-container') : $('.' + collection, '.collection-container');
    protoHtml = protoHtml.clone();
    protoHtml.children('label').remove();
    var proto = protoHtml.html();
    for (var i = 0; i < level; i++) {
      var parentIndex = parentIndexes[i];
      proto = proto.replace(
        new RegExp('__NAME' + i + '__', 'g'),
        parentIndex ? parentIndex : 0
      );
    }
    proto = proto.replace(new RegExp('__NAME' + level + '__', 'g'), newIndex);
    proto = proto.replace(/__NAME[\d]+__/g, 0);
    container.append(proto);
  });

  /* Removes form on click to Remove This button */
  $('form').delegate('.remove_from_collection', 'click', function () {
    var _this = $(this);

    if ($('#removeDialog').length === 0) {
      $('body').append('' +
      '<div class="modal" id="removeDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">' +
      '<div class="modal-dialog">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<h4 class="modal-title" id="myModalLabel"></h4>' +
      '</div>' +
      '<div class="modal-body"></div>' +
      '<div class="modal-footer"></div>' +
      '</div>' +
      '</div>' +
      '</div>');
    }

    var removeDialog = $('#removeDialog');

    var buttons = '' +
    '<button class="btn btn-primary btn_remove" type="button">Yes</button>' +
    '<button class="btn btn-default" type="button"  data-dismiss="modal">No</button>';

    $('.modal-header .modal-title', removeDialog).html('Remove Confirmation');
    $('.modal-body', removeDialog).html('Are you sure you want to remove this block?');
    $('.modal-footer', removeDialog).html(buttons);

    $('body').undelegate('.btn_remove', 'click').delegate('.btn_remove', 'click', function () {
      var collectionForm = _this.parents('.collection_form').eq(0);
      _this.parent('.form-group').remove();
      if ($('> .form-group', collectionForm).length === 0) {
        collectionForm.next('.add_to_collection').trigger('click');
      }
      removeDialog.modal('hide');
    });

    removeDialog.modal('show');
  });

  var language = $.cookie('language');
  language = language ? language : 'en';
  $('.language-select-wrap .flag-icon-' + language).parent().addClass('active');
  $('.language-select-wrap .flag-wrapper').click(function () {
    $.cookie('language', $(this).attr('data-lang'), {path: '/'});
    window.location.reload();
  });

  $('input[name="organization_user_identifier"]').keyup(function () {
    $('input[name="username"]').val($(this).val() + '_admin');
  });

  $('input[name="activity_identifier"]').keyup(function () {
    $('input[name="iati_identifier_text"]').val($('#reporting_organization_identifier').text() + '-' + $(this).val());
  });

  if ($('input[name="activity_identifier"]').val() !== '') {
    $('input[name="activity_identifier"]').trigger('keyup');
  }

  $('.checkAll').click(function () {
    $('.field1').not('[readonly="readonly"]').prop('checked', this.checked);
  });

  $('input[type="checkbox"][readonly="readonly"]').change(function () {
    $(this).prop('checked', !this.checked);
  });
  $('form').delegate('select[readonly=readonly]', 'mousedown', function (e) {
    e.preventDefault();
  });

  /*
  * Confirmation for form submission
  * Usage:
  * Define Submit button params as:
  *   type = "button"
  *   class="btn_confirm"
  *   data-title="confirmation title" (optional)
  *   data-message="confirmation message"
  * */
  $('.btn_confirm').click(function () {

    var title = $(this).attr('data-title');
    var message = $(this).attr('data-message');
    var formId = $(this).parents('form').attr('id');

    if ($('#popDialog').length === 0) {
      $('body').append('' +
      '<div class="modal" id="popDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">' +
      '<div class="modal-dialog">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<h4 class="modal-title" id="myModalLabel"></h4>' +
      '</div>' +
      '<div class="modal-body"></div>' +
      '<div class="modal-footer"></div>' +
      '</div>' +
      '</div>' +
      '</div>');
    }

    var popElem = $('#popDialog');

    if (title === undefined) {
      $('.modal-header', popElem).addClass('hidden').children('.modal-title').html('');
    }
    else {
      $('.modal-header', popElem).removeClass('hidden').children('.modal-title').html(title);
    }

    $('.modal-body', popElem).html(message);

    var buttons = '' +
    '<button class="btn btn-primary btn_yes" type="button">Yes</button>' +
    '<button class="btn btn-default" type="button"  data-dismiss="modal">No</button>';

    $('.modal-footer', popElem).html(buttons);

    $('body').undelegate('.btn_yes', 'click').delegate('.btn_yes', 'click', function () {
      $('#' + formId).submit();
    });

    popElem.modal('show');
  });

  /* change the sector field according to the  sector vocabulary selected */
  $("form").delegate('.sector_vocabulary', 'change', function () {
    var parent = $(this).parent('.form-group');
    var vocabulary = $(this).val();
    var sectorClass = ['.sector_text', '.sector_select', '.sector_category_select'];
    var selectedSector = sectorClass[vocabulary] ? sectorClass[vocabulary] : sectorClass[0];
    parent.siblings('.sector_types').addClass('hidden');
    parent.siblings(selectedSector).removeClass('hidden');
  });
  $('.sector_vocabulary').trigger('change');

  /* generate admin username using organization user identifier while adding new organization by superadmin*/
  $('#organization_user_identifier').keyup(function () {
    $('#admin_username').val($(this).val() + '_admin');
  });

  /* generate group admin username using organization identifier while grouping organizations by superadmin*/
  $('#group_identifier').keyup(function () {
    $('#group_admin_username').val($(this).val() + '_group');
  });

  /* return panel for result modal */
  function getPanel($title) {
    var elemRow = $('#view_result .modal-panel-template').clone();
    $('.panel-heading', elemRow).html($title);
    return elemRow;
  }

  /* return row for result modal panel */
  function getRow(label, value) {
    var elemRow = $('#view_result .modal-row-template').clone();
    $('.view_label', elemRow).html(label);
    $('.view_value', elemRow).html(value);
    return elemRow.html();
  }

  /* display result modal with data */
  $('#view_result').on('show.bs.modal', function (event) {
    var result = JSON.parse($(event.relatedTarget).attr('data-result'));
    var modalContent = '';
    modalContent += getRow('Type:', result.type);
    modalContent += getRow('Aggregation Status:', result.aggregation_status);
    var title = getPanel('Title');
    $('.panel-body', title).append(getRow('Text:', result.title[0].narrative[0].narrative));
    modalContent += title.html();
    var description = getPanel('Description');
    $('.panel-body', description).append(getRow('Text:', result.description[0].narrative[0].narrative));
    modalContent += description.html();
    $('.modal-body', this).html(modalContent);
  });

  $('.delete').click(function (e) {
    e.preventDefault();
    var location = this.href;

    if ($('#delDialog').length === 0) {
      $('body').append('' +
      '<div class="modal" id="delDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">' +
      '<div class="modal-dialog">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<h4 class="modal-title" id="myModalLabel"></h4>' +
      '</div>' +
      '<div class="modal-body"></div>' +
      '<div class="modal-footer"></div>' +
      '</div>' +
      '</div>' +
      '</div>');
    }

    var delDialog = $('#delDialog');

    var buttons = '' +
    '<button class="btn btn-primary btn_del" type="button">Yes</button>' +
    '<button class="btn btn-default" type="button"  data-dismiss="modal">No</button>';

    $('.modal-header .modal-title', delDialog).html('Delete Confirmation');
    $('.modal-body', delDialog).html('Are you sure you want to delete?');
    $('.modal-footer', delDialog).html(buttons);

    $('body').undelegate('.btn_del', 'click').delegate('.btn_del', 'click', function () {
      window.location = location;
    });

    delDialog.modal('show');

  });


 $('[data-toggle="tooltip"]').tooltip({
     position: {
       my: "center bottom-20",
       at: "center top",
       using: function( position, feedback ) {
         $( this ).css( position );
         $( "<div>" )
           .addClass( "arrow" )
           .addClass( feedback.vertical )
           .addClass( feedback.horizontal )
           .appendTo( this );
       }
     }
   });


  var documentHeight = $(document).height();
  $('.element-sidebar-wrapper').css('height', documentHeight);

  $('form').delegate('.remove_from_collection', 'mouseenter mouseleave', function () {
    $(this).parent('.form-group').toggleClass('fill-border');
  });

  $('.element-sidebar-wrapper .panel-body li a').hover(function () {
    $(this).children('.action-icon').css('display', 'block');
  },
  function () {
    $(this).children('.action-icon').css('display', 'none');
  });

  $('.sidebar-wrapper .nav').hover(function () {
    $('.sidebar-wrapper').addClass('full-sidebar-wrapper');
  },
  function () {
    $('.sidebar-wrapper').removeClass('full-sidebar-wrapper');
  });

    //js for form input check and leave page alert
    var preventNavigation = false;
    $('form').delegate('textarea, select, input:not(".ignore_change")', 'change keyup', function () {
        preventNavigation = true;
    });

  $('.language-selector').click(function () {
    $(this).siblings('.language-flag-wrap').toggle();
  });


  //js for form input check and leave page alert
  var preventNavigation = false;
  $('form').delegate('textarea, select, input:not(".ignore_change")', 'change keyup', function (e) {
    preventNavigation = true;
  });

  $('[type="submit"]').click(function () {
    preventNavigation = false;
  });


  window.onbeforeunload = function () {
    if (preventNavigation) {
      return 'You have unsaved changes.';
    }
  };

  $('.element-menu-wrapper').click(function(){
    $(this).children('.element-sidebar-wrapper').toggle();
  });

  $(document).mouseup(function (e)
  {
      var container = $('.language-flag-wrap');
      if ( !container.is(e.target)
          && container.has(e.target).length === 0)
      {
          container.hide();
      }
  });


  $(".clickable-row").click(function() {
      window.document.location = $(this).data("href");
  });

  $(".clickable-row > td > :checkbox").click(function(e){
    e.stopPropagation();
    $(this).parents('.clickable-row').toggleClass('clickable-row-bg');
  });


});
