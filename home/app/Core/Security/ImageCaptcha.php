<?php
namespace Bulletpoint\Core\Security;

use Bulletpoint\Core\Filesystem;
use Bulletpoint\Exception;

final class ImageCaptcha implements Captcha {
	private $randomness;
	private $size;

	public function __construct(string $randomness = null) {
		$this->randomness = $randomness ?: bin2hex(random_bytes(2));
		$this->size = new Filesystem\Size(50, 30);
	}

	public function verify(string $answer) {
		if(trim($answer) !== $this->randomness) {
			throw new Exception\AccessDeniedException(
				'Opsaný text z obrázku není správný'
			);
		}
	}

	public function __toString() {
		$image = imagecreate($this->size->width(), $this->size->height());
		imagesetstyle($image, [imagecolorallocate($image, 0, 0, 0)]);
		imagestring(
			$image,
			5,
			5,
			5,
			$this->randomness,
			imagecolorallocate($image, 255, 255, 255)
		);
		ob_start();
		imagepng($image);
		$data = ob_get_clean();
		imagedestroy($image);
		return sprintf(
			'<img src="data:image/png;base64,%s"%s>',
			base64_encode($data),
			(string)$this->size
		);
	}
}