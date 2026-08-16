<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Campaign.php';
require_once __DIR__ . '/models/User.php';

require_login();
$user = current_user();
$campaignModel = new Campaign(getDB());

$errors = [];
$old = [
    'patient_name' => '', 'age' => '', 'cancer_type' => '', 'cancer_stage' => '',
    'hospital' => '', 'treatment_details' => '', 'target_amount' => '',
    'story' => '', 'contact_information' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid session token. Please try again.';
    } else {
        foreach ($old as $key => $_) {
            $old[$key] = trim($_POST[$key] ?? '');
        }

        if (mb_strlen($old['patient_name']) < 2) $errors[] = 'Please enter the patient name.';
        if (!ctype_digit($old['age']) || (int)$old['age'] < 0 || (int)$old['age'] > 120) $errors[] = 'Please enter a valid age.';
        if ($old['cancer_type'] === '') $errors[] = 'Please enter the cancer type.';
        if ($old['cancer_stage'] === '') $errors[] = 'Please enter the cancer stage.';
        if ($old['hospital'] === '') $errors[] = 'Please enter the hospital name.';
        if (!is_numeric($old['target_amount']) || (float)$old['target_amount'] < 1000) $errors[] = 'Please enter a valid required amount (minimum ৳1,000).';
        if (mb_strlen($old['story']) < 30) $errors[] = 'Please provide a more detailed patient story (at least 30 characters).';
        if ($old['contact_information'] === '') $errors[] = 'Please provide contact information.';

        $photoFilename = null;
        if (!empty($_FILES['patient_photo']['name'])) {
            [$ok, $msg] = validate_image_upload($_FILES['patient_photo']);
            if (!$ok) {
                $errors[] = $msg;
            } else {
                $photoFilename = safe_filename($_FILES['patient_photo']['name']);
                if (!move_uploaded_file($_FILES['patient_photo']['tmp_name'], UPLOAD_PATIENTS_DIR . $photoFilename)) {
                    $errors[] = 'Failed to upload patient photo. Please try again.';
                    $photoFilename = null;
                }
            }
        }

        $documentFiles = [];
        if (!empty($_FILES['medical_documents']['name'][0])) {
            $count = count($_FILES['medical_documents']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['medical_documents']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                $file = [
                    'name' => $_FILES['medical_documents']['name'][$i],
                    'type' => $_FILES['medical_documents']['type'][$i],
                    'tmp_name' => $_FILES['medical_documents']['tmp_name'][$i],
                    'error' => $_FILES['medical_documents']['error'][$i],
                    'size' => $_FILES['medical_documents']['size'][$i],
                ];
                [$ok, $msg] = validate_document_upload($file);
                if (!$ok) {
                    $errors[] = 'Document ' . ($i + 1) . ': ' . $msg;
                    continue;
                }
                $documentFiles[] = $file;
            }
        }

        if (!$errors) {
            $campaignId = $campaignModel->create([
                'user_id' => $user['id'],
                'patient_name' => $old['patient_name'],
                'patient_photo' => $photoFilename,
                'age' => (int)$old['age'],
                'cancer_type' => $old['cancer_type'],
                'cancer_stage' => $old['cancer_stage'],
                'hospital' => $old['hospital'],
                'treatment_details' => $old['treatment_details'],
                'target_amount' => (float)$old['target_amount'],
                'story' => $old['story'],
                'contact_information' => $old['contact_information'],
            ]);

            foreach ($documentFiles as $file) {
                $docFilename = safe_filename($file['name']);
                if (move_uploaded_file($file['tmp_name'], UPLOAD_DOCUMENTS_DIR . $docFilename)) {
                    $campaignModel->addDocument($campaignId, $file['name'], $docFilename, $file['type']);
                }
            }

            if ($user['role'] === 'donor') {
                (new User(getDB()))->promoteToFundraiser($user['id']);
            }

            flash('success', 'Your fundraiser has been submitted and is pending admin verification.');
            redirect('dashboard.php');
        }
    }
}

$pageTitle = 'Create Fundraiser';
include __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="form-card form-wide">
    <h1>Create a Fundraiser</h1>
    <p>Fill in accurate patient details. Your campaign will be reviewed by our admin team before it goes public.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="create_campaign.php" enctype="multipart/form-data" class="js-loading-state">
      <?= csrf_field() ?>

      <div class="form-row">
        <div class="form-group">
          <label for="patient_name">Patient Name</label>
          <input type="text" id="patient_name" name="patient_name" class="form-control" required value="<?= e($old['patient_name']) ?>">
        </div>
        <div class="form-group">
          <label for="age">Age</label>
          <input type="number" id="age" name="age" class="form-control" min="0" max="120" required value="<?= e($old['age']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="patient_photo">Patient Photo</label>
        <input type="file" id="patient_photo" name="patient_photo" class="form-control image-input" data-preview="photoPreview" accept="image/jpeg,image/png,image/webp">
        <img id="photoPreview" alt="Preview" style="display:none; margin-top:10px; max-width:180px; border-radius:8px;">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="cancer_type">Cancer Type</label>
          <input type="text" id="cancer_type" name="cancer_type" class="form-control" required value="<?= e($old['cancer_type']) ?>" placeholder="e.g. Blood Cancer">
        </div>
        <div class="form-group">
          <label for="cancer_stage">Cancer Stage</label>
          <input type="text" id="cancer_stage" name="cancer_stage" class="form-control" required value="<?= e($old['cancer_stage']) ?>" placeholder="e.g. Stage 2">
        </div>
      </div>

      <div class="form-group">
        <label for="hospital">Hospital Name</label>
        <input type="text" id="hospital" name="hospital" class="form-control" required value="<?= e($old['hospital']) ?>">
      </div>

      <div class="form-group">
        <label for="treatment_details">Treatment Details</label>
        <textarea id="treatment_details" name="treatment_details" class="form-control"><?= e($old['treatment_details']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="target_amount">Required Amount (<?= CURRENCY_SYMBOL ?>)</label>
        <input type="number" id="target_amount" name="target_amount" class="form-control" min="1000" step="0.01" required value="<?= e($old['target_amount']) ?>">
      </div>

      <div class="form-group">
        <label for="story">Patient Story</label>
        <textarea id="story" name="story" class="form-control" required rows="6"><?= e($old['story']) ?></textarea>
        <div class="form-hint">Explain the patient's situation and why financial assistance is needed.</div>
      </div>

      <div class="form-group">
        <label for="contact_information">Contact Information</label>
        <input type="text" id="contact_information" name="contact_information" class="form-control" required value="<?= e($old['contact_information']) ?>" placeholder="Email / phone for verification">
      </div>

      <div class="form-group">
        <label for="medical_documents">Medical Documents (reports, doctor's notes, treatment estimate)</label>
        <input type="file" id="medical_documents" name="medical_documents[]" class="form-control" accept="image/jpeg,image/png,image/webp,application/pdf" multiple>
        <div class="form-hint">These documents are private and only visible to admins for verification. PDF, JPG, PNG accepted (max 10MB each).</div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Submit for Verification</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
