<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Authorized Students Attendance List</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #000;
        background-color: #fff;
        margin: 10px;
      }

      .watermark {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('{{ env(' SCHOOL_LOGO') }}') center center no-repeat;
        background-size: 50%;
        opacity: 0.05;
        z-index: 0;
        pointer-events: none;
      }


      .content {
        position: relative;
        z-index: 1;
      }

      .header {
        text-align: center;
        margin-bottom: 10px;
      }

      .header img {
        height: 50px;
        margin-bottom: 5px;
      }

      .course-info h3 {
        margin: 3px 0;
        font-size: 13px;
      }

      .course-info p {
        margin: 0;
        font-size: 11px;
      }

      h2.title {
        text-align: center;
        font-size: 13px;
        margin: 10px 0;
        text-transform: uppercase;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 10px;
      }

      th,
      td {
        border: 1px solid #000;
        padding: 4px;
        word-wrap: break-word;
      }

      th {
        background-color: #eee;
        font-weight: bold;
        text-align: center;
      }

      td img {
        width: 25px;
        height: 25px;
        object-fit: cover;
        border-radius: 2px;
        display: block;
        margin: auto;
      }

      /* Specific column widths
      .col-sno {
        padding: 5px;
      }

      .col-sex {
        padding: 15px;
      }

      .col-level {
        padding: 10px;
      } */

      th:nth-child(1),
      td:nth-child(1) {
            width: 20px !important; /* SN - Small */
        }

      @media print {
        body {
          margin: 10mm;
        }

        .header img {
          height: 40px;
        }

        table {
          font-size: 9.5px;
        }

        th,
        td {
          border: 1px solid #000;
        }
      }
    </style>
  </head>

  <body>

    <div class="watermark"></div>


    <div class="content">
      <div class="header">
        <td style="border: none; width: 50%;">
          <img src="{{ env('SCHOOL_LOGO') }}" width="20%">
        </td>
        <div class="course-info">
          <h3>{{ $course->name }} - ({{ $course->code }})</h3>
          <p>Academic Session: {{ $academicSession }}</p>
        </div>
      </div>

      <h2 class="title">{{ $programmeCategory }} Examination Attendance List</h2>

      <table>
        <thead>
          <tr>
            <th class="col-sno">S/No</th>
            <th>Passport</th>
            <th>Matric No</th>
            <th>Full Name</th>
            <th class="col-sex">Sex</th>
            <th class="col-level">Level</th>
            <th>Faculty</th>
            <th>Department</th>
            <th>Programme</th>
            <th>Sign In</th>
            <th>Sign Out</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $entry)
          {{-- @dd($entry['student']->matric_number, $loop->iteration); --}}
          <tr>
            <td class="col-sno">{{ $loop->iteration }}</td>
            <td>
              <img
                src="{{ !empty($entry['student']->image) ? $entry['student']->image : asset('assets/images/users/user-dummy-img.jpg') }}"
                alt="Passport">
            </td>
            <td>{{ $entry['student']->matric_number ?? 'N/A' }}</td>
            <td>
              {{ optional($entry['student']->applicant)->lastname }}
              {{ optional($entry['student']->applicant)->othernames }}
            </td>
            <td>{{ optional($entry['student']->academicLevel)->level ?? '' }}</td>
            <td>{{ optional($entry['student']->faculty)->name ?? '' }}</td>
            <td>{{ optional($entry['student']->department)->name ?? '' }}</td>
            <td>{{ optional($entry['student']->programme)->award ?? '' }}</td>

            <td></td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <br><br>

      <h3 style="color: black; font-weight: bold;">
        For Official Use Only – Not to be filled by students
      </h3>

      <table
        style="width: 100%; margin-top: 30px; font-size: 11px; text-align: center; border-collapse: separate; border-spacing: 20px 10px;">
        <tr>
          <td
            style="border-bottom: 1px solid #000; height: 40px; border-left:none; border-right:none; border-top: none;">
          </td>
          <td style="border-bottom: 1px solid #000; border-left:none; border-right:none; border-top: none;"></td>
          <td style="border-bottom: 1px solid #000; border-left:none; border-right:none; border-top: none;"></td>
        </tr>
        <tr>
          <th style="border: none; background-color: transparent;">Chief Supervisor's Name</th>
          <th style="border: none; background-color: transparent;">Signature</th>
          <th style="border: none; background-color: transparent;">Date</th>
        </tr>
      </table>


    </div>

  </body>

</html>