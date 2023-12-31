<?php

namespace Drupal\domain_registration\Tests;

use Drupal\Tests\BrowserTestBase;

/**
 * Test if the Domain Registration module allows and denies specified domains.
 *
 * @group domain_registration
 */
class DomainRegistrationTest extends BrowserTestBase {

  protected static $modules = ['domain_registration'];

  /**
   * Tests allowing exact match.
   */
  public function testAllowExactMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_ALLOW)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', 'example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a whitelist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('A welcome message'));
  }

  /**
   * Tests allowing empty pattern match.
   */
  public function testAllowEmptyPatternMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_ALLOW)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', '')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a whitelist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('A welcome message'));
  }

  /**
   * Tests allowing empty wildcard match.
   */
  public function testAllowWildcardMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_ALLOW)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', '*.example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@subdomain.example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a whitelist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('A welcome message'));
  }

  /**
   * Tests disallowing no match.
   */
  public function testDisallowNoMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_ALLOW)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', 'example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@otherexample.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a non whitelist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('You are not allowed to register for this site.'));
  }

  /**
   * Tests denying exact match.
   */
  public function testDenyExactMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_DENY)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', 'example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a blacklist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('You are not allowed to register for this site.'));
  }

  /**
   * Tests denying wildcard match.
   */
  public function testDenyWildcardMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_DENY)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', '*.example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@subdomain.example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with blacklist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('You are not allowed to register for this site.'));
  }

  /**
   * Tests custom message on deny.
   */
  public function testDenyCustomMessage() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_DENY)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('message', 'foo bar baz')->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', 'example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@example.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a blacklist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('foo bar baz'));
  }

  /**
   * Tests denying with no match.
   */
  public function testDenyNoMatch() {
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('method', DOMAIN_REGISTRATION_DENY)->save();
    \Drupal::configFactory()->getEditable('domain_registration.settings')->set('pattern', 'example.com')->save();

    // Get the user data for registration.
    $edit = [
      'name' => $this->randomMachineName(),
      'mail' => $this->randomMachineName() . '@otherexample.com',
    ];
    $this->drupalGet('user/register');
    // Attempt to register a user with a non blacklist email.
    $this->submitForm($edit, t('Create new account'));
    $this->assertSession()->pageTextContains(t('A welcome message'));
  }

}
